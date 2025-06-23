<?php
// app/Http/Controllers/RowController.php

namespace App\Http\Controllers;

use App\Data\RowItem;
use App\Data\MetaData;
use App\Services\DrupalApiService;
use Illuminate\Support\Str;

class RowController extends Controller
{
    public function show(string $locale, string $slug, DrupalApiService $drupal, int $tabIndex = 0)
    {
        app()->setLocale($locale);

        $allRows = $drupal->getReihen();
        $row = collect($allRows)
            ->map(fn(array $raw) => RowItem::fromDrupal($raw))
            ->first(fn(RowItem $r) => trim($r->slug()) === trim($slug));

        if (!$row) {
            abort(404);
        }

        $projectItemsArray = $row->projectsFromFieldProjekteReihe($locale, $drupal);

        $projects = collect($projectItemsArray);

        $meta = new MetaData(
            title: 'Peira – ' . $row->localizedTitle($locale),
            titleEn: 'Peira – ' . $row->localizedTitle('en'),
            description: Str::limit(strip_tags($row->localizedBody($locale)), 160),
            descriptionEn: Str::limit(strip_tags($row->localizedBody('en')), 160),
        );

        $subinfos = $row->subinfosFromFieldLinks($drupal);
        $activeTab = min(max($tabIndex, 0), count($subinfos));

        $tagsRow = $row->tagLabels($locale, $drupal);

        return view('rows.show', [
            'locale'      => $locale,
            'row'         => $row,
            'projects'    => $projects,
            'meta'        => $meta,
            'tagsProject' => $tagsRow,
            'activeTab'   => $activeTab,
            'subinfos'    => $subinfos,
            'contributors'=> [],
            'coproducers' => [],
            'funders'     => [],
        ]);
    }
}
