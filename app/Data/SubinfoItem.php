<?php

namespace App\Data;

use App\Services\DrupalApi;

class SubinfoItem
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $klarTitle = null,
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
            bodyHtml: DrupalApi::get($item, 'body', 'processed'),
            bodyHtmlEn: DrupalApi::get($item, 'field_bodyenglish', 'processed'),
            subtitleEn: DrupalApi::get($item, 'field_subtitle_en', 'processed'),
            links: $item['field_linkssub'] ?? [],
            linkedProjectNids: $linkedNids,
            lang: DrupalApi::get($item, 'langcode') ?? 'de',
            raw: $item
        );
    }
}