<?php

namespace App\Data;

use App\Services\DrupalApi;

class CoProducerItem
{
    public function __construct(
        public string  $id,
        public string  $name,
        public ?string $url     = null,
        public ?string $logoUrl = null,
        public ?string $logoAlt = null,
    ) {}

    public static function fromDrupal(array $item): self
    {
        return new self(
            id:      DrupalApi::get($item, 'nid'),
            name:    DrupalApi::get($item, 'field_name') ?? DrupalApi::get($item, 'title'),
            url:     DrupalApi::get($item, 'field_link'),
            logoUrl: DrupalApi::get($item, 'field_logo', 'url'),
            logoAlt: DrupalApi::get($item, 'field_logo', 'alt'),
        );
    }

    public function getLink(): ?string
    {
        return $this->url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function getLogoAlt(): ?string
    {
        return $this->logoAlt;
    }

    public function asHtmlLink(): string
    {
        if ($this->url && $this->name) {
            return "<a href=\"{$this->url}\" target=\"_blank\" title=\"{$this->name}\">{$this->name}</a>";
        }

        return $this->name ?: 'TBA';
    }
}