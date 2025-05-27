<?php

namespace App\Data;

use Illuminate\Support\Str;
use App\Services\DrupalApiService;
use App\Services\TagHelper;
use App\Services\DrupalApi;

class ProjectItem
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $titleEn,
        public ?string $bodyHtml,
        public ?string $bodyHtmlEn,
        public ?string $year,
        public ?string $imageUrl,
        public array $tags = [],
        public bool $overlay = true,
        public bool $darkText = false,
        public string $style = '',
        public string $lang = 'de',
        public array $raw = [],
        public array $images = [],
        public string $place = '',
    ) {
    }

    public static function fromDrupal(array $item, string $locale = 'de'): self
    {
        $tags = [];
        if (!empty($item['field_tags'])) {
            foreach ($item['field_tags'] as $tag) {
                $tags[] = $tag['target_id'];
            }
        }

        return new self(
            id: DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title'),
            titleEn: DrupalApi::get($item, 'field_titel_projekt_en'),
            bodyHtml: DrupalApi::getProcessed($item, 'body'),
            bodyHtmlEn: DrupalApi::getProcessed($item, 'field_bodyenglish'),
            year: DrupalApi::get($item, 'field_jahr_der_'),
            imageUrl: DrupalApi::get($item, 'field_titel', 'url'),
            tags: $tags,
            overlay: filter_var(DrupalApi::get($item, 'field_bildoverlay'), FILTER_VALIDATE_BOOLEAN),
            darkText: filter_var(DrupalApi::get($item, 'field_schwarzertext'), FILTER_VALIDATE_BOOLEAN),
            style: DrupalApi::get($item, 'field_projektstil') ?? '',
            lang: DrupalApi::get($item, 'langcode') ?? 'de',
            raw: $item,
            images: DrupalApi::getArray($item, 'field_fotostrecke'),
            place: DrupalApi::get($item, 'field_ort') ?? '',
        );
    }

    public function slug(): string
    {
        return Str::slug($this->title);
    }

    public function url(string $locale): string
    {
        return '/' . $locale . '/projekte/' . $this->slug();
    }

    public function localizedTitle(string $locale): string
    {
        return $locale === 'en' && $this->titleEn ? $this->titleEn : $this->title;
    }

    public function localizedBody(string $locale): ?string
    {
        return $locale === 'en' && $this->bodyHtmlEn ? $this->bodyHtmlEn : $this->bodyHtml;
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

    public function tagLabels(string $locale): array
    {
        $tags = app(DrupalApiService::class)->getTags();
        return TagHelper::labels($tags, $this->tags, $locale);
    }

    public function subinfosFromFieldLinks(): array
    {
        $api = app(\App\Services\DrupalApiService::class);
        $nids = collect($this->raw['field_links'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        \Log::info('Subinfo target_ids:', $nids);

        return collect($nids)
            ->map(fn(int $nid) => $api->getSubinfoByNid($nid))
            ->filter()
            ->map(fn($raw) => $raw ? \App\Data\SubinfoItem::fromDrupal($raw) : null)
            ->filter()
            ->values()
            ->all();
    }

    public function images(): array
    {
        return $this->raw['field_fotostrecke'] ?? [];
    }

    public function dates(): array
    {
        $api = app(\App\Services\DrupalApiService::class);

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
}