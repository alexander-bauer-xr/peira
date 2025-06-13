<?php
// app/Data/RowItem.php

namespace App\Data;

use Illuminate\Support\Str;
use App\Services\DrupalApiService;
use App\Services\TagHelper;
use App\Services\DrupalApi;

class RowItem
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $titleEn = null,
        public ?string $bodyHtml = null,
        public ?string $bodyHtmlEn = null,
        public ?string $year = null,
        public ?string $imageUrl = null,
        public array $tags = [],
        public bool $overlay = true,
        public bool $darkText = false,
        public string $style = '',
        public string $lang = 'de',
        public array $raw = [],
        public array $socialMediaItems = [],

    ) {
    }

    public static function fromDrupal(array $item): self
    {
        $tags = [];
        if (!empty($item['field_tags'])) {
            foreach ($item['field_tags'] as $tag) {
                $tags[] = $tag['target_id'];
            }
        }

        return new self(
            id: DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title'),
            titleEn: DrupalApi::get($item, 'field_titel_reihe_en'),
            bodyHtml: DrupalApi::getProcessed($item, 'body'),
            bodyHtmlEn: DrupalApi::getProcessed($item, 'field_bodyenglish'),
            year: DrupalApi::get($item, 'field_jahr_der_'),
            imageUrl: DrupalApi::get($item, 'field_titelbild', 'url'),
            tags: $tags,
            overlay: filter_var(DrupalApi::get($item, 'field_bildoverlay'), FILTER_VALIDATE_BOOLEAN),
            darkText: filter_var(DrupalApi::get($item, 'field_weisser_text'), FILTER_VALIDATE_BOOLEAN),
            style: DrupalApi::get($item, 'field_projektstil') ?? '',
            lang: DrupalApi::get($item, 'langcode') ?? 'de',
            raw: $item,
            socialMediaItems: DrupalApi::getSocialMedia($item, 'field_social_media'),

        );
    }

    public function slug(): string
    {
        return Str::slug($this->title);
    }

    public function url(string $locale): string
    {
        return '/' . $locale . '/reihen/' . $this->slug();
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

    public function tagLabels(string $locale): array
    {
        $tags = app(DrupalApiService::class)->getTags();
        return TagHelper::labels($tags, $this->tags, $locale);
    }

    public function subinfosFromFieldLinks(): array
    {
        $api = app(DrupalApiService::class);
        $nids = collect($this->raw['field_link_reihen'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        return collect($nids)
            ->map(fn(int $nid) => $api->getSubinfoByNid($nid))
            ->filter() // remove null/empty
            ->map(fn(array $raw) => SubinfoItem::fromDrupal($raw))
            ->values()
            ->all();
    }

    public function projectsFromFieldProjekteReihe(string $locale): array
    {
        $api = app(DrupalApiService::class);

        $nids = collect($this->raw['field_projekte_reihe'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        return collect($nids)
            ->map(fn(int $nid) => $api->getById($nid)[0] ?? null)
            ->filter(fn($raw) => is_array($raw) && !empty($raw))
            ->map(fn(array $raw) => ProjectItem::fromDrupal($raw, $locale))
            ->values()
            ->all();
    }

    public function socialMedia(): string
    {
        $entries = $this->socialMediaItems;
        if (empty($entries)) {
            return '';
        }

        $html = '<div class="termine">';
        $html .= '<div class="newshead small-text">' . __('content.social') . '</div>';
        $html .= '<div class="social-media-grid">';

        foreach ($entries as $entry) {
            $label = e($entry['first'] ?? '');
            $handle = e($entry['second'] ?? '');
            $url = $entry['third'] ?? null;

            if (!$label || !$url) {
                continue;
            }

            $html .= '<div class="social-media-row d-flex flex-row gap-2">';
            $html .= '<div class="social-media-first">' . $label . '</div>';
            $html .= '<a href="' . e($url) . '" target="_blank" class="social-media-second">'
                . $handle . ' </a>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}