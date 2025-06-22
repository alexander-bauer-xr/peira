<?php

namespace App\Data;

use App\Services\DrupalApi;
use Illuminate\Support\Str;

class SubinfoItem
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $klarTitle = null,
        public ?string $klarTitleEn = null,
        public ?string $bodyHtml = null,
        public ?string $bodyHtmlEn = null,
        public ?string $subtitleEn = null,
        public array $links = [],
        public array $linkedProjectNids = [],
        public string $lang = 'de',
        public array $raw = []
    ) {
    }

    public static function fromDrupal(array $item): self
    {
        $linkedNids = collect($item['field_links'] ?? [])
            ->pluck('target_id')
            ->map(fn($id) => strval($id))
            ->all();

        return new self(
            id: DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title'),
            klarTitle: DrupalApi::get($item, 'field_klartitel'),
            klarTitleEn: DrupalApi::get($item, 'field_klartitel_en'),
            bodyHtml: DrupalApi::get($item, 'body', 'processed'),
            bodyHtmlEn: DrupalApi::get($item, 'field_bodyenglish', 'processed'),
            subtitleEn: DrupalApi::get($item, 'field_subtitle_en', 'processed'),
            links: $item['field_linkssub'] ?? [],
            linkedProjectNids: $linkedNids,
            lang: DrupalApi::get($item, 'langcode') ?? 'de',
            raw: $item
        );
    }

    public function localizedTitle(string $locale): string
    {
        return $locale === 'en' && $this->klarTitleEn ? $this->klarTitleEn : $this->klarTitle;
    }

    public function localizedBody(string $locale): ?string
    {
        return $locale === 'en' && $this->bodyHtmlEn ? $this->bodyHtmlEn : $this->bodyHtml;
    }

    public function slug(): string
    {
        return Str::slug($this->localizedTitle($this->lang));
    }
}