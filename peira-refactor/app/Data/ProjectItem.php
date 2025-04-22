<?php

namespace App\Data;

use Illuminate\Support\Str;
use App\Services\DrupalApiService;
use App\Services\TagHelper;

class ProjectItem
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $titleEn,
        public ?string $bodyHtml,
        public ?string $bodyHtmlEn,
        public ?string $year,
        public ?string $imageUrl,
        public array $tags = [],
        public bool $overlay = true,
        public bool $darkText = false,
        public string $style = '',
        public string $lang = 'de',
    ) {
    }

    public static function fromDrupal(array $item, string $locale = 'de'): self
    {
        $get = fn(string $key, string $subkey = 'value') => $item[$key][0][$subkey] ?? null;

        $tags = [];
        if (!empty($item['field_tags'])) {
            foreach ($item['field_tags'] as $tag) {
                $tags[] = $tag['target_id'];
            }
        }

        return new self(
            id: $get('nid'),
            title: $get('title'),
            titleEn: $get('field_titel_projekt_en'),
            bodyHtml: $item['body'][0]['processed'] ?? null,
            bodyHtmlEn: $item['field_bodyenglish'][0]['processed'] ?? null,
            year: $get('field_jahr_der_'),
            imageUrl: $item['field_titel'][0]['url'] ?? null,
            tags: $tags,
            overlay: filter_var($get('field_bildoverlay'), FILTER_VALIDATE_BOOLEAN),
            darkText: filter_var($get('field_schwarzertext'), FILTER_VALIDATE_BOOLEAN),
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

    public function yearFormatted(): ?string
    {
        return $this->year ? date('Y', strtotime($this->year)) : null;
    }

    public function tagLabels(string $locale): array
    {
        $tags = app(\App\Services\DrupalApiService::class)->getTags();
        return TagHelper::labels($tags, $this->tags, $locale);
    }
}