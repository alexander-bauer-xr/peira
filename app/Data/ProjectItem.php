<?php

namespace App\Data;

use App\Services\DrupalApiService;
use App\Services\TagHelper;
use App\Services\DrupalApi;
use App\Services\SocialMediaRenderer;

use App\Data\RowItem;
use App\Data\SubinfoItem;

class ProjectItem extends BaseContentItem
{
    public function __construct(
        public string $id,
        string $title,
        ?string $titleEn,
        ?string $bodyHtml,
        ?string $bodyHtmlEn,
        public ?string $year,
        public ?string $imageUrl,
        public ?string $image_uuid,
        public array $tags = [],
        public bool $overlay = true,
        public bool $darkText = false,
        public string $style = '',
        string $lang = 'de',
        public array $raw = [],
        public array $images = [],
        public string $place = '',
        public array $socialMediaItems = [],
    ) {
        parent::__construct($title, $titleEn, $bodyHtml, $bodyHtmlEn, $lang);
    }

    public static function fromDrupal(array $item, string $locale = 'de'): self
    {
        $tags = [];
        if (!empty($item['field_tags'])) {
            foreach ($item['field_tags'] as $tag) {
                $tags[] = $tag['target_id'];
            }
        }

        $gallery = collect($item['field_fotostrecke'] ?? [])
            ->map(fn($g) => [
                'uuid' => $g['target_uuid'] ?? null,
                'alt' => $g['alt'] ?? '',
                'title' => $g['title'] ?? '',
            ])
            ->filter(fn($g) => $g['uuid'])
            ->values()
            ->all();

        $gallery = $item['field_fotostrecke'] ?? [];

        $cover = $item['field_titel'][0] ?? [];

        return new self(
            id: DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title'),
            titleEn: DrupalApi::get($item, 'field_titel_projekt_en'),
            bodyHtml: DrupalApi::getProcessed($item, 'body'),
            bodyHtmlEn: DrupalApi::getProcessed($item, 'field_bodyenglish'),
            year: DrupalApi::get($item, 'field_jahr_der_'),
            imageUrl: $cover['url'] ?? null,
            image_uuid: $cover['target_uuid'] ?? null,
            tags: $tags,
            overlay: filter_var(DrupalApi::get($item, 'field_bildoverlay'), FILTER_VALIDATE_BOOLEAN),
            darkText: filter_var(DrupalApi::get($item, 'field_schwarzertext'), FILTER_VALIDATE_BOOLEAN),
            style: DrupalApi::get($item, 'field_projektstil') ?? '',
            lang: DrupalApi::get($item, 'langcode') ?? 'de',
            raw: $item,
            images: $gallery,
            place: DrupalApi::get($item, 'field_ort') ?? '',
            socialMediaItems: DrupalApi::getSocialMedia($item, 'field_social_media'),
        );
    }

    public function url(string $locale): string
    {
        return '/' . $locale . '/projekte/' . $this->slug();
    }

    public function yearFormatted(): ?string
    {
        return $this->year ? date('Y', strtotime($this->year)) : null;
    }

    public function yearAndPlace(): string
    {
        $year = $this->yearFormatted();
        return $year ? ($this->place ? "$year, $this->place" : $year) : $this->place;
    }

    public function tagLabels(string $locale, DrupalApiService $api): array
    {
        $tags = $api->getTags();
        return TagHelper::labels($tags, $this->tags, $locale);
    }

    public function subinfosFromFieldLinks(DrupalApiService $api): array
    {
        $nids = collect($this->raw['field_links'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        \Log::info('Subinfo target_ids:', $nids);

        return collect($nids)
            ->map(fn(int $nid) => $api->getSubinfoByNid($nid))
            ->filter()
            ->map(fn($raw) => $raw ? SubinfoItem::fromDrupal($raw) : null)
            ->filter()
            ->values()
            ->all();
    }

    public function images(): array
    {
        return $this->raw['field_fotostrecke'] ?? [];
    }
    public function galleryUuids(): array
    {
        return array_column($this->images, 'target_uuid');
    }

    public function imageUuid(): ?string
    {
        return $this->image_uuid;
    }

    public function dates(DrupalApiService $api): array
    {
        $termineData = $api->getTermine();

        $coproList = collect($api->getFoerdererUndKoproduzenten())
            ->mapWithKeys(fn($p) => [
                strval($p['nid'][0]['value']) => CoProducerItem::fromDrupal($p)
            ]);

        return collect($this->raw['field_termine'] ?? [])
            ->pluck('target_id')
            ->map(function ($terminNid) use ($termineData, $coproList, $api) {

                $termin = collect($termineData)
                    ->first(fn($t) => ($t['nid'][0]['value'] ?? null) == $terminNid);

                if (!$termin) {
                    return null;
                }

                $placeId = $termin['field_veranstaltungsort'][0]['target_id'] ?? null;

                $place = $placeId
                    ? (
                        $coproList->get(strval($placeId))
                        ?? optional(
                            $api->getFoerdererUndKoproduzentByNid($placeId),
                            fn($raw) =>
                            $coproList[strval($placeId)] = CoProducerItem::fromDrupal($raw)
                        )
                    )
                    : null;

                return TermineItem::fromDrupal(
                    $termin,
                    $place ?? new CoProducerItem('', 'TBA')
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    public function reihe(DrupalApiService $api): ?RowItem
    {
        $reihen = $api->getReihen();

        \Log::debug('[reihe()] Suche nach Reihe für Projekt-ID: ' . $this->id);

        foreach ($reihen as $reihe) {
            $referenzen = $reihe['field_projekte_reihe'] ?? [];

            foreach ($referenzen as $referenz) {
                if (($referenz['target_id'] ?? null) == $this->id) {
                    \Log::debug('[reihe()] Gefunden in Reihe: ' . ($reihe['nid'][0]['value'] ?? '??'));
                    return RowItem::fromDrupal($reihe);
                }
            }
        }

        \Log::debug('[reihe()] Keine Reihe gefunden für Projekt-ID: ' . $this->id);
        return null;
    }

    public function contributors(): array
    {
        $fieldName = ($this->lang === 'en')
            ? 'field_contributors'
            : 'field_mitwirkende';

        $rawList = $this->raw[$fieldName] ?? [];
        $out = [];

        foreach ($rawList as $entry) {
            $first = trim($entry['first'] ?? '');
            $second = trim($entry['second'] ?? '');
            $third = trim($entry['third'] ?? '');

            if ($first || $second) {
                $out[] = [
                    'first' => $first,
                    'second' => $second,
                    'third' => $third !== '' ? $third : null,
                ];
            }
        }

        return $out;
    }

    public function funders(DrupalApiService $api): array
    {
        $nids = collect($this->raw['field_foerderer'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        return collect($nids)
            ->map(fn(int $nid) => $api->getFoerdererUndKoproduzentByNid($nid))
            ->filter()
            ->map(fn(array $raw) => CoProducerItem::fromDrupal($raw))
            ->values()
            ->all();
    }

    public function coProducers(DrupalApiService $api): array
    {
        $nids = collect($this->raw['field_kooperationspartner'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        return collect($nids)
            ->map(fn(int $nid) => $api->getFoerdererUndKoproduzentByNid($nid))
            ->filter()
            ->map(fn(array $raw) => CoProducerItem::fromDrupal($raw))
            ->values()
            ->all();
    }

    public function sponsors(DrupalApiService $api): array
    {
        return array_merge($this->funders($api), $this->coProducers($api));
    }

    public function socialMedia(SocialMediaRenderer $renderer): string
    {
        return $renderer->render($this->socialMediaItems);
    }

    public function coverStyles(): array 
    {
        if (!$this->image_uuid) {
            return [];
        }

        $api = app(DrupalApiService::class);

        return $api->getFileByUuid($this->image_uuid)['data']['attributes']['image_style_uri']
            ?? [];
    }
}