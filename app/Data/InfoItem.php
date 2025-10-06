<?php

namespace App\Data;

use App\Services\DrupalApiService;
use App\Services\DrupalApi;
use Illuminate\Support\Str;

class InfoItem extends BaseContentItem
{
    public function __construct(
        public int $id,
        string $title,
        ?string $titleEn,
        ?string $bodyHtml,
        ?string $bodyHtmlEn,
        public ?string $imageUrl,
        public array $raw,
        string $lang = 'de',
    ) {
        parent::__construct($title, $titleEn, $bodyHtml, $bodyHtmlEn, $lang);
    }

    public static function fromDrupal(array $item, string $locale = 'de'): self
    {
        return new self(
            id: (int) DrupalApi::get($item, 'nid'),
            title: DrupalApi::get($item, 'title') ?? '',
            titleEn: DrupalApi::get($item, 'field_titel_projekt_en'),
            bodyHtml: DrupalApi::getProcessed($item, 'body'),
            bodyHtmlEn: DrupalApi::getProcessed($item, 'field_bodyenglish'),
            imageUrl: DrupalApi::get($item, 'field_titel', 'url'),
            raw: $item,
            lang: $locale,
        );
    }

    public function url(string $locale): string
    {
        $slugDe = Str::slug($this->title ?? '');
        $slugEn = $this->titleEn ? Str::slug($this->titleEn) : null;

        $basePath = in_array('fortbildung', [$slugDe, $slugEn], true)
            ? 'fortbildung'
            : 'ueber-uns';

        return '/' . $locale . '/' . $basePath . '/' . $this->slug();
    }

    public function subinfosFromFieldLinks(DrupalApiService $api): array
    {

        $nids = collect($this->raw['field_links'] ?? [])
            ->pluck('target_id')
            ->filter()
            ->map(fn($id) => intval($id))
            ->all();

        return collect($nids)
            ->map(fn(int $nid) => $api->getSubinfoByNid($nid))
            ->filter()
            ->map(fn($raw) => $raw ? SubinfoItem::fromDrupal($raw, $this->lang) : null)
            ->filter()
            ->values()
            ->all();
    }
}