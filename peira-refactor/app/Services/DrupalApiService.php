<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DrupalApiService
{
    protected string $baseUrl = 'https://www.peira.space/web/api';

    protected function cachedRequest(string $endpoint, string $key = null, int $minutes = 10): array
    {
        $locale = app()->getLocale(); // optional, remove if not needed
        $cacheKey = $key ?? "api.{$endpoint}.{$locale}";

        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($endpoint) {
            return Http::get("{$this->baseUrl}/{$endpoint}")->json();
        });
    }

    public function getFoerdererUndKoproduzenten(): array
    {
        return $this->cachedRequest('foerdererkoproduzenten');
    }

    public function getInfos(): array
    {
        return $this->cachedRequest('infos');
    }

    public function getNews(): array
    {
        return $this->cachedRequest('news');
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
}