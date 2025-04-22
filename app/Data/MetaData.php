<?php

namespace App\Data;

class MetaData
{
    public function __construct(
        public string $title,
        public ?string $titleEn,
        public string $description,
        public ?string $descriptionEn,
    ) {}

    public function localizedTitle(string $locale): string
    {
        return $locale === 'en' && $this->titleEn ? $this->titleEn : $this->title;
    }

    public function localizedDescription(string $locale): string
    {
        return $locale === 'en' && $this->descriptionEn ? $this->descriptionEn : $this->description;
    }
}