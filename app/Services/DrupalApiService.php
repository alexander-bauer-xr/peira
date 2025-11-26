<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DrupalApiService
{
    protected string $baseUrl;
    protected string $drupalBase;

    public function __construct()
    {
        $this->drupalBase = rtrim(config('services.drupal.base_url', config('app.url')), '/');
        $this->baseUrl = $this->drupalBase ? "{$this->drupalBase}/api" : '/api';
    }

    /**
     * Prefix relative image style URIs with the Drupal base URL so derivatives
     * (including token-protected variants) are requested from Drupal instead
     * of the Laravel host.
     */
    protected function absolutizeImageStyles(array $file): array
    {
        $styles = data_get($file, 'data.attributes.image_style_uri');

        if (!is_array($styles)) {
            return $file;
        }

        $file['data']['attributes']['image_style_uri'] = collect($styles)
            ->map(function ($url) {
                if (!is_string($url) || str_starts_with($url, 'http')) {
                    return $url;
                }

                return str_starts_with($url, '/')
                    ? "{$this->drupalBase}{$url}"
                    : $url;
            })
            ->all();

        return $file;
    }

    /**
     * Request fresh image style URLs with correct itok tokens from Drupal.
     * This method fetches the image derivative URLs directly from Drupal's image style endpoint,
     * ensuring each URL has the correct itok security token.
     */
    protected function refreshImageStyleTokens(string $uuid, array $styles): array
    {
        if (empty($styles)) {
            return [];
        }

        $refreshed = [];
        
        foreach ($styles as $styleName => $url) {
            try {
                // Request the image style derivative URL from Drupal
                // This will generate a fresh itok token
                $response = $this->authenticatedRequest()
                    ->get("{$this->baseUrl}/json/file/file/{$uuid}");
                
                if ($response->successful()) {
                    $freshData = $response->json();
                    $freshStyles = data_get($freshData, 'data.attributes.image_style_uri', []);
                    
                    if (isset($freshStyles[$styleName])) {
                        $refreshed[$styleName] = $freshStyles[$styleName];
                    } else {
                        $refreshed[$styleName] = $url;
                    }
                } else {
                    Log::warning('Failed to refresh itok for style', [
                        'uuid' => $uuid,
                        'style' => $styleName,
                        'status' => $response->status()
                    ]);
                    $refreshed[$styleName] = $url;
                }
            } catch (\Exception $e) {
                Log::error('Error refreshing image style token', [
                    'uuid' => $uuid,
                    'style' => $styleName,
                    'error' => $e->getMessage()
                ]);
                $refreshed[$styleName] = $url;
            }
        }
        
        return $refreshed;
    }

    /**
     * Strip itok parameters from image URLs.
     * Use this ONLY if Drupal is configured with $settings['image_allow_insecure_derivatives'] = TRUE
     * 
     * @param array $file
     * @return array
     */
    protected function stripItokParams(array $file): array
    {
        $styles = data_get($file, 'data.attributes.image_style_uri');

        if (!is_array($styles)) {
            return $file;
        }

        $file['data']['attributes']['image_style_uri'] = collect($styles)
            ->map(function ($url) {
                if (!is_string($url)) {
                    return $url;
                }
                
                // Remove itok parameter
                $url = preg_replace('/([?&])itok=[^&]*(&?)/', '$1', $url);
                
                // Clean up trailing ? or &
                $url = rtrim($url, '?&');
                
                // If only ? remains after cleaning, remove it
                if (str_ends_with($url, '?')) {
                    $url = substr($url, 0, -1);
                }
                
                return $url;
            })
            ->all();

        return $file;
    }

    protected function getCsrfToken(): string
    {
        return Cache::remember('drupal.csrf.token', now()->addMinutes(30), function () {
            $tokenUrl = "{$this->drupalBase}/session/token";
            return Http::get($tokenUrl)->body();
        });
    }

    protected function authenticatedRequest()
    {
        $username = config('services.drupal.username');
        $password = config('services.drupal.password');

        if (!$username || !$password) {
            throw new \RuntimeException(
                'Drupal API credentials not configured. Please set DRUPAL_API_USERNAME and DRUPAL_API_PASSWORD in your .env file.'
            );
        }

        return Http::withHeaders([
            'X-CSRF-Token' => $this->getCsrfToken(),
        ])->withBasicAuth($username, $password);
    }

    protected function cachedRequest(string $endpoint, ?string $key = null, int $minutes = 10, bool $authenticated = false): array
    {
        $locale = app()->getLocale();
        $cacheKey = $key ?? "api.{$endpoint}.{$locale}";

        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($endpoint, $authenticated) {
            $client = $authenticated ? $this->authenticatedRequest() : Http::acceptJson();
            $response = $client->get("{$this->baseUrl}/{$endpoint}");

            if (!$response->successful()) {
                \Log::error('Drupal API request failed', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \RuntimeException(
                    "Drupal API request failed for endpoint '{$endpoint}': " .
                    $response->status() . ' - ' . substr($response->body(), 0, 200)
                );
            }

            return $response->json() ?? [];
        });
    }

    public function getFoerdererUndKoproduzenten(): array
    {
        return $this->cachedRequest('foerdererkoproduzenten');
    }
    public function getFoerdererUndKoproduzentByNid(int $nid): ?array
    {
        return $this->cachedRequest(
            "foerdererkoproduzenten?nid={$nid}",
            "api.foerderer.nid.{$nid}",
            60
        )[0] ?? null;
    }

    public function getInfos(): array
    {
        return $this->cachedRequest('infos');
    }

    public function getNews(): array
    {
        return $this->cachedRequest('news');
    }

    public function getArchivedNews(): array
    {
        return $this->cachedRequest('newsarchiv');
    }

    public function getProjekte(): array
    {
        return $this->cachedRequest('projekte');
    }

    public function getReihen(): array
    {
        return $this->cachedRequest('reihen');
    }

    public function getReihung(): array
    {
        return $this->cachedRequest('reihung');
    }

    public function getReihungEn(): array
    {
        return $this->cachedRequest('reihung_en');
    }

    public function getSubinfo(): array
    {
        return $this->cachedRequest('subinfo');
    }

    public function getSubinfoByNid(int $nid): ?array
    {
        return $this->cachedRequest(
            "subinfo?nid={$nid}",
            "api.subinfo.nid.{$nid}",
            60
        )[0] ?? null;
    }

    public function getTags(): array
    {
        return $this->cachedRequest('tags');
    }

    public function getTagsProjekte(): array
    {
        return $this->cachedRequest('protax');
    }

    public function getTermine(): array
    {
        return $this->cachedRequest('termine');
    }

    public function getVideos(): array
    {
        return $this->cachedRequest('videos');
    }

    public function getByEndpoint(string $endpoint): array
    {
        return $this->cachedRequest($endpoint);
    }

    public function getById(int $nid): array
    {
        return $this->cachedRequest("projekte?nid={$nid}", "api.projekte.nid.{$nid}");
    }

    public function getFileByUuid(string $uuid, bool $refreshTokens = false): array
    {
        $cacheKey = "api.json/file/file/{$uuid}." . app()->getLocale();
        
        // If refreshTokens is true, clear the cache to get fresh data
        if ($refreshTokens) {
            Cache::forget($cacheKey);
        }
        
        $file = $this->cachedRequest("json/file/file/{$uuid}", $cacheKey, 10, true);
        $file = $this->absolutizeImageStyles($file);
        
        // Optional: Strip itok parameters if Drupal is configured to allow insecure derivatives
        // Uncomment the line below ONLY if you have set $settings['image_allow_insecure_derivatives'] = TRUE in Drupal
        // $file = $this->stripItokParams($file);
        
        // Log the image style URIs for debugging
        if (config('app.debug')) {
            $styles = data_get($file, 'data.attributes.image_style_uri', []);
            Log::debug('Drupal image styles fetched', [
                'uuid' => $uuid,
                'styles' => array_map(function($url) {
                    if (!is_string($url)) {
                        return ['url' => $url, 'has_itok' => false, 'itok' => null];
                    }
                    // Log whether itok is present
                    $hasItok = str_contains($url, 'itok=');
                    return [
                        'url' => $url,
                        'has_itok' => $hasItok,
                        'itok' => $hasItok ? (preg_match('/[?&]itok=([^&]+)/', $url, $m) ? $m[1] : null) : null
                    ];
                }, $styles)
            ]);
        }
        
        return $file;
    }
}