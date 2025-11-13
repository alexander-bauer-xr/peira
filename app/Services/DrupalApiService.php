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
            $client = $authenticated ? $this->authenticatedRequest() : Http::asJson();
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

    public function getFileByUuid(string $uuid): array
    {
        return $this->cachedRequest("json/file/file/{$uuid}", null, 10, true);
    }
}