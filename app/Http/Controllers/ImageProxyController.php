<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ImageProxyController extends Controller
{
    public function show(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            abort(400, 'Missing url parameter');
        }

        // Decode URL if it's double-encoded
        if (strpos($url, '%25') !== false) {
            $url = urldecode($url);
        }

        $drupalBase = rtrim(config('services.drupal.base_url'), '/');
        if (!str_starts_with($url, $drupalBase)) {
            \Log::error('Image proxy: Invalid image source', [
                'url' => $url,
                'drupalBase' => $drupalBase
            ]);
            abort(403, 'Invalid image source');
        }

        $cacheKey = 'image.proxy.' . md5($url);

        try {
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($url, $drupalBase) {
                // Parse the image style URL
                // Format: https://cms.../sites/default/files/styles/STYLE_NAME/public/path/to/image.jpg.webp
                $parsed = $this->parseImageStyleUrl($url);

                if (!$parsed) {
                    \Log::error('Image proxy: Failed to parse image URL', ['url' => $url]);
                    abort(400, 'Invalid image style URL format');
                }

                // Call Drupal's image derivative API endpoint
                // No auth needed - endpoint is configured with _access: 'TRUE'
                $response = Http::timeout(30)
                    ->post($drupalBase . '/api/image-derivative', [
                        'file_uri' => $parsed['file_uri'],
                        'style' => $parsed['style'],
                        'return_mode' => 'binary'
                    ]);

                if (!$response->successful()) {
                    \Log::error('Image proxy: Failed to generate derivative', [
                        'file_uri' => $parsed['file_uri'],
                        'style' => $parsed['style'],
                        'status' => $response->status(),
                        'body' => substr($response->body(), 0, 200)
                    ]);
                    abort($response->status(), 'Failed to generate image derivative');
                }

                $contentType = $response->header('Content-Type') ?: 'image/jpeg';
                $body = $response->body();

                return response($body, 200)
                    ->header('Content-Type', $contentType)
                    ->header('Cache-Control', 'public, max-age=86400')
                    ->header('Etag', md5($body));
            });
        } catch (\Exception $e) {
            \Log::error('Image proxy exception', [
                'url' => $url,
                'message' => $e->getMessage()
            ]);
            abort(500, 'Image proxy error: ' . $e->getMessage());
        }
    }

    /**
     * Parse image style URL to extract style name and file URI
     *
     * @param string $url The full image style URL
     * @return array|null Array with 'style' and 'file_uri' or null if parsing fails
     */
    private function parseImageStyleUrl($url)
    {
        // Remove query string (itok parameter)
        $urlWithoutQuery = strtok($url, '?');

        // Pattern: /sites/default/files/styles/STYLE_NAME/public/path/to/file.ext.format
        // Example: /sites/default/files/styles/peira_desktop_lg_webp/public/2023-09/image.jpg.webp
        $pattern = '#/sites/default/files/styles/([^/]+)/(public/.+)$#';

        if (!preg_match($pattern, $urlWithoutQuery, $matches)) {
            return null;
        }

        $style = $matches[1];
        $filePath = $matches[2];

        // Remove the format extension added by the image style (.webp or .jpeg)
        // The original file doesn't have this double extension
        $filePath = preg_replace('/\.(webp|jpeg)$/', '', $filePath);

        // Decode URL-encoded characters (e.g., %C2%A9 → ©, %C3%B6 → ö)
        $filePath = urldecode($filePath);

        // Convert path to Drupal URI format (public://path)
        $fileUri = str_replace('public/', 'public://', $filePath);

        return [
            'style' => $style,
            'file_uri' => $fileUri
        ];
    }
}
