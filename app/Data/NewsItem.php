<?php

namespace App\Data;

use App\Services\DrupalApi;

class NewsItem extends BaseContentItem
{
    public function __construct(
        public string $id,
        string $title,
        ?string $titleEn,
        ?string $bodyHtml,
        ?string $bodyHtmlEn,
        public ?string $date,
        string $lang,
        public bool $showDate = false,
        public bool $showTime = false,
    ) {
        parent::__construct($title, $titleEn, $bodyHtml, $bodyHtmlEn, $lang);
    }
    
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

}