<?php

namespace App\Data;

use Illuminate\Support\Str;
use App\Services\DrupalApiService;
use App\Services\DrupalApi;

class InfoItem
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $titleEn,
        public ?string $bodyHtml,
        public ?string $bodyHtmlEn,
        public ?string $imageUrl,
        public array $raw,
        public string $lang = 'de',
    ) {
    }

    public static function fromDrupal(array $item, string $locale = 'de'): self
    {
        return new self(
            id: (int) DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title') ?? '',
            titleEn: DrupalApi::get($item, 'field_titel_projekt_en'),
            bodyHtml: DrupalApi::getProcessed($item, 'body'),
            bodyHtmlEn: DrupalApi::getProcessed($item, 'field_bodyenglish'),
            imageUrl: DrupalApi::get($item, 'field_titel', 'url'),
            raw: $item,
            lang: $locale,
        );
    }

    public function slug(): string
    {
        return Str::slug($this->localizedTitle($this->lang));
    }

    public function url(string $locale): string
    {
        return '/' . $locale . '/ueber-uns/' . $this->slug();
    }

    public function localizedTitle(string $locale): string
    {
        return $locale === 'en' && $this->titleEn
            ? $this->titleEn
            : $this->title;
    }

    public function localizedBody(string $locale): ?string
    {
        return $locale === 'en' && $this->bodyHtmlEn
            ? $this->bodyHtmlEn
            : $this->bodyHtml;
    }

    public function subinfosFromFieldLinks(): array
    {
        $api = app(DrupalApiService::class);

        $nids = collect($this->raw['field_links'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        return collect($nids)
            ->map(fn(int $nid) => $api->getSubinfoByNid($nid))
            ->filter()
            ->map(fn($raw) => $raw ? SubinfoItem::fromDrupal($raw, $this->lang) : null)
            ->filter()
            ->values()
            ->all();
    }
}