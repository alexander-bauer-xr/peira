<?php

namespace App\Data;

use Illuminate\Support\Str;

abstract class BaseContentItem
{
    public function __construct(
        public string $title,
        public ?string $titleEn = null,
        public ?string $bodyHtml = null,
        public ?string $bodyHtmlEn = null,
        public string $lang = 'de',
    ) {
    }

    public function slug(): string
    {
        return Str::slug($this->localizedTitle($this->lang));
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
}
