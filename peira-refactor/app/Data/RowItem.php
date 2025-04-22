<?php

namespace App\Data;

use Illuminate\Support\Str;
use App\Services\DrupalApiService;
use App\Services\TagHelper;

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
    ) {}

    public static function fromDrupal(array $item): self
    {
        $get = fn(string $key, string $subkey = 'value') =>
            $item[$key][0][$subkey] ?? null;

        $tags = [];
        if (!empty($item['field_tags'])) {
            foreach ($item['field_tags'] as $tag) {
                $tags[] = $tag['target_id'];
            }
        }

        return new self(
            id: $get('nid'),
            title: $get('title'),
            titleEn: $get('field_titel_reihe_en'),
            bodyHtml: $item['body'][0]['processed'] ?? null,
            bodyHtmlEn: $item['field_bodyenglish'][0]['processed'] ?? null,
            year: $get('field_jahr_der_'), // ← falls du das Feld irgendwann doch brauchst
            imageUrl: $item['field_titelbild'][0]['url'] ?? null,
            tags: $tags,
            overlay: filter_var($get('field_bildoverlay'), FILTER_VALIDATE_BOOLEAN),
            darkText: filter_var($get('field_weisser_text'), FILTER_VALIDATE_BOOLEAN),
            style: $get('field_projektstil') ?? '',
            lang: $get('langcode') ?? 'de',
        );
    }

    public function slug(): string
    {
        return Str::slug($this->title);
    }

    public function localizedTitle(string $locale): string
    {
        return $locale === 'en' && $this->titleEn ? $this->titleEn : $this->title;
    }

    public function localizedBody(string $locale): ?string
    {
        return $locale === 'en' && $this->bodyHtmlEn ? $this->bodyHtmlEn : $this->bodyHtml;
    }

    public function tagLabels(string $locale): array
    {
        $tags = app(DrupalApiService::class)->getTags();
        return TagHelper::labels($tags, $this->tags, $locale);
    }
}