<?php
// app/Http/Controllers/RowController.php

namespace App\Http\Controllers;

use App\Data\RowItem;
use App\Data\ProjectItem;
use App\Data\MetaData;
use App\Services\DrupalApiService;
use Illuminate\Support\Str;

class RowController extends Controller
{
    public function show(string $locale, string $slug, DrupalApiService $drupal)
    {
        app()->setLocale($locale);

        $allRows = $drupal->getReihen();
        $row = collect($allRows)
            ->map(fn(array $raw) => RowItem::fromDrupal($raw))
            ->first(fn(RowItem $r) => trim($r->slug()) === trim($slug));

        if (! $row) {
            abort(404);
        }

        $allProjects = $drupal->getProjekte();
        $projects = collect($allProjects)
            ->map(fn(array $raw) => ProjectItem::fromDrupal($raw, $locale))
            ->filter(fn(ProjectItem $p) =>
                collect($p->raw['field_reihen'] ?? [])
                    ->pluck('target_id')
                    ->contains(intval($row->id))
            )
            ->values();

        $meta = new MetaData(
            title:         'Peira – ' . $row->localizedTitle($locale),
            titleEn:       'Peira – ' . $row->localizedTitle('en'),
            description:   Str::limit(strip_tags($row->localizedBody($locale)), 160),
            descriptionEn: Str::limit(strip_tags($row->localizedBody('en')),   160),
        );

        $tagsRow = $row->tagLabels($locale);

        return view('rows.show', [
            'locale'   => $locale,
            'row'      => $row,
            'projects' => $projects,
            'meta'     => $meta,
            'tagsProject'=> $tagsRow,
        ]);
    }
}
