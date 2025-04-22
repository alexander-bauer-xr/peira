<?php

namespace App\Data;

use App\Services\DrupalApiService;

class ReihungItem
{
    public function __construct(
        public string $title,
        public array $projectIds, // Nur nid-Werte
    ) {}

    public static function fromDrupal(array $item): self
    {
        $title = $item['title'][0]['value'] ?? '';
        $projectIds = array_map(
            fn($entry) => $entry['target_id'],
            $item['field_projekte'] ?? []
        );

        return new self(
            title: $title,
            projectIds: $projectIds
        );
    }

    public function entries(string $locale): array
    {
        $drupal = app(DrupalApiService::class);

        return collect($this->projectIds)
            ->map(function ($nid) use ($drupal, $locale) {
                $raw = $drupal->getById($nid);

                if (!empty($raw) && isset($raw[0])) {
                    $item = $raw[0];
                    $type = $item['type'][0]['target_id'] ?? null;

                    return match ($type) {
                        'projekt' => ProjectItem::fromDrupal($item),
                        'zitat'   => ZitatItem::fromDrupal($item),
                        'reihen'   => RowItem::fromDrupal($item),
                        default   => null,
                    };
                }

                return null;
            })
            ->filter()
            ->toArray();
    }
}
