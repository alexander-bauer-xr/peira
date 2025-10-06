<?php

namespace App\Http\Controllers;

use App\Data\MetaData;
use App\Data\InfoItem;
use App\Services\DrupalApiService;
use Illuminate\Support\Str;

class InfoController extends Controller
{
    public function about(DrupalApiService $drupal, string $locale = 'de', ?string $tabSlug = null)
    {
        $meta = new MetaData(
            title: 'Peira - Über uns',
            titleEn: 'Peira - About us',
            description: 'Informationen über das Team, Kontaktmöglichkeiten und Datenschutz.',
            descriptionEn: 'Information about the team, contact details and privacy.'
        );

        return $this->renderPage(
            drupal: $drupal,
            locale: $locale,
            tabSlug: $tabSlug,
            matchingTitles: ['Über uns', 'Über Uns', 'About', 'About us'],
            meta: $meta,
            pageKey: 'about',
            routeSegment: 'ueber-uns'
        );
    }

    public function fortbildung(DrupalApiService $drupal, string $locale = 'de', ?string $tabSlug = null)
    {
        $meta = new MetaData(
            title: 'Peira - Fortbildung',
            titleEn: 'Peira - Training',
            description: 'Workshops, Formate und Fortbildungsangebote von Peira.',
            descriptionEn: 'Workshops, formats and training opportunities by Peira.'
        );

        return $this->renderPage(
            drupal: $drupal,
            locale: $locale,
            tabSlug: $tabSlug,
            matchingTitles: ['Fortbildung', 'Fortbildungen', 'Training', 'Workshops', 'Professional Development'],
            meta: $meta,
            pageKey: 'fortbildung',
            routeSegment: 'fortbildung'
        );
    }

    private function renderPage(
        DrupalApiService $drupal,
        string $locale,
        ?string $tabSlug,
        array $matchingTitles,
        MetaData $meta,
        string $pageKey,
        string $routeSegment
    ) {
        app()->setLocale($locale);

        $matches = collect($matchingTitles)
            ->map(fn($title) => Str::lower($title))
            ->filter()
            ->values()
            ->all();

        $infos = collect($drupal->getInfos())
            ->map(fn($raw) => InfoItem::fromDrupal($raw, $locale));

        $item = $infos->first(function (InfoItem $info) use ($matches) {
            $candidates = array_filter([
                $info->title,
                $info->titleEn,
            ]);

            foreach ($candidates as $candidate) {
                if (in_array(Str::lower($candidate), $matches, true)) {
                    return true;
                }
            }

            return false;
        });

        if (! $item) {
            abort(404);
        }

        $subinfosCollection = collect($item->subinfosFromFieldLinks($drupal));
        $subinfos = $subinfosCollection
            ->mapWithKeys(fn($sub) => [$slug = $sub->slug() => $sub])
            ->all();

        $slugs = array_keys($subinfos);

        $activeTab = 0;
        if ($tabSlug && in_array($tabSlug, $slugs, true)) {
            $activeTab = array_search($tabSlug, $slugs, true);
        }

        $pageTitle = $item->localizedTitle($locale);

        return view('info.page', compact(
            'item',
            'locale',
            'meta',
            'subinfos',
            'slugs',
            'activeTab',
            'pageTitle',
            'pageKey',
            'routeSegment'
        ));
    }
}