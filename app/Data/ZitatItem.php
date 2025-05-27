<?php

namespace App\Data;

use App\Services\DrupalApi;

class ZitatItem
{
    public function __construct(
        public string $title,
        public ?string $titleEn,
        public ?string $body,
        public ?string $bodyEn
    ) {}

    public static function fromDrupal(array $item): self
    {

        return new self(
            title: DrupalApi::get($item,'title'),
            titleEn: DrupalApi::get($item,'field_titel_zitat_en'),
            body: DrupalApi::getProcessed($item,'body') ?? null,
            bodyEn: DrupalApi::getProcessed($item,'field_bodyenglish') ?? null,
        );
    }

    public function localizedTitle(string $locale): string
    {
        return $locale === 'en' && $this->titleEn ? $this->titleEn : $this->title;
    }

    public function localizedBody(string $locale): ?string
    {
        return $locale === 'en' && $this->bodyEn ? $this->bodyEn : $this->body;
    }
}