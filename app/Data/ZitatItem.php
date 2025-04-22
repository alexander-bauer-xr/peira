<?php

namespace App\Data;

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
        $get = fn(string $key, string $subkey = 'value') =>
            $item[$key][0][$subkey] ?? null;

        return new self(
            title: $get('title'),
            titleEn: $get('field_titel_zitat_en'),
            body: $item['body'][0]['processed'] ?? null,
            bodyEn: $item['field_bodyenglish'][0]['processed'] ?? null,
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