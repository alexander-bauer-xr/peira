<?php

namespace App\Data;

use Illuminate\Support\Str;
use App\Services\DrupalApi;

class NewsItem
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $titleEn,
        public ?string $bodyHtml,
        public ?string $bodyHtmlEn,
        public ?string $date,
        public string $lang,
        public bool $showDate = false,
        public bool $showTime = false,
    ) {}
    
    public static function fromDrupal(array $item): self
    {
        $get = fn(string $key, string $subkey = 'value') =>
            $item[$key][0][$subkey] ?? null;
    
        return new self(
            id: DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title'),
            titleEn: DrupalApi::get($item, 'field_titel_en'),
            bodyHtml: DrupalApi::getProcessed($item, 'body'),
            bodyHtmlEn: DrupalApi::getProcessed($item, 'field_bodyenglish'),
            date: DrupalApi::get($item, 'field_datum'),
            lang: DrupalApi::get($item, 'langcode'),
            showDate: filter_var(DrupalApi::get($item, 'field_datum_anzeigen_'), FILTER_VALIDATE_BOOLEAN),
            showTime: filter_var(DrupalApi::get($item, 'field_datum_mit_uhrzeit_anzeigen'), FILTER_VALIDATE_BOOLEAN),
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
}