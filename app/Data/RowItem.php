<?php
// app/Data/RowItem.php

namespace App\Data;

use App\Services\DrupalApiService;
use App\Services\TagHelper;
use App\Services\DrupalApi;
use App\Services\SocialMediaRenderer;

class RowItem extends BaseContentItem
{
    public function __construct(
        public string $id,
        string $title,
        ?string $titleEn = null,
        ?string $bodyHtml = null,
        ?string $bodyHtmlEn = null,
        public ?string $year = null,
        public ?string $imageUrl = null,
        public ?string $image_uuid = null,
        public array $tags = [],
        public bool $overlay = true,
        public bool $darkText = false,
        public string $style = '',
        string $lang = 'de',
        public array $raw = [],
        public array $socialMediaItems = [],

    ) {
        parent::__construct($title, $titleEn, $bodyHtml, $bodyHtmlEn, $lang);
    }

    public static function fromDrupal(array $item): self
    {
        $tags = [];
        if (!empty($item['field_tags'])) {
            foreach ($item['field_tags'] as $tag) {
                $tags[] = $tag['target_id'];
            }
        }

        $cover = $item['field_titelbild'][0] ?? [];

        return new self(
            id: DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title'),
            titleEn: DrupalApi::get($item, 'field_titel_reihe_en'),
            bodyHtml: DrupalApi::getProcessed($item, 'body'),
            bodyHtmlEn: DrupalApi::getProcessed($item, 'field_bodyenglish'),
            year: DrupalApi::get($item, 'field_jahr_der_'),
            imageUrl: $cover['url'] ?? null,
            image_uuid: $cover['target_uuid'] ?? null,
            tags: $tags,
            overlay: filter_var(DrupalApi::get($item, 'field_bildoverlay'), FILTER_VALIDATE_BOOLEAN),
            darkText: filter_var(DrupalApi::get($item, 'field_weisser_text'), FILTER_VALIDATE_BOOLEAN),
            style: DrupalApi::get($item, 'field_projektstil') ?? '',
            lang: DrupalApi::get($item, 'langcode') ?? 'de',
            raw: $item,
            socialMediaItems: DrupalApi::getSocialMedia($item, 'field_social_media'),

        );
    }

    public function url(string $locale): string
    {
        return '/' . $locale . '/reihen/' . $this->slug();
    }


    public function tagLabels(string $locale, DrupalApiService $api): array
    {
        $tags = $api->getTags();
        return TagHelper::labels($tags, $this->tags, $locale);
    }

    public function subinfosFromFieldLinks(DrupalApiService $api): array
    {
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

    public function imageUuid(): ?string
    {
        return $this->image_uuid;
    }

    public function projectsFromFieldProjekteReihe(string $locale, DrupalApiService $api): array
    {

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

    public function socialMedia(SocialMediaRenderer $renderer): string
    {
        return $renderer->render($this->socialMediaItems);
    }
}