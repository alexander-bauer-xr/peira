<?php

namespace App\Data;

class CoProducerItem
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $url = null,
    ) {}

    public static function fromDrupal(array $item): self
    {
        $get = fn(string $key) => $item[$key][0]['value'] ?? null;

        return new self(
            id: $get('nid'),
            name: $get('field_name') ?? $get('title'),
            url: $get('field_link'),
        );
    }

    public function asHtmlLink(): string
    {
        if ($this->url && $this->name) {
            return "<a href=\"{$this->url}\" target=\"_blank\" title=\"{$this->name}\">{$this->name}</a>";
        }

        return $this->name ?: 'TBA';
    }
}