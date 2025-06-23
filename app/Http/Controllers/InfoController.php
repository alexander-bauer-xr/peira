<?php

namespace App\Http\Controllers;

use App\Data\MetaData;
use App\Data\InfoItem;
use App\Services\DrupalApiService;
use Illuminate\Support\Str;

class InfoController extends Controller
{
    public function index(DrupalApiService $drupal, string $locale = 'de', string $tabSlug = null)
    {
        app()->setLocale($locale);

        $infos = collect($drupal->getInfos())
            ->map(fn($raw) => InfoItem::fromDrupal($raw, $locale));

        $item = $infos->first(fn($i) =>
            Str::lower($i->title) === 'über uns' || Str::lower($i->title) === 'about'
        );

        $subinfos = collect($item->subinfosFromFieldLinks($drupal))
            ->mapWithKeys(fn($sub) => [
                $slug = $sub->slug() => $sub
            ])->all();

        $slugs = array_keys($subinfos);

        $activeTab = 0;
        if ($tabSlug && in_array($tabSlug, $slugs, true)) {
            $activeTab = array_search($tabSlug, $slugs, true);
        }

        $meta = new MetaData(
            title: 'Peira - Über uns',
            titleEn: 'Peira - About us',
            description: 'Informationen über das Team, Kontaktmöglichkeiten und Datenschutz.',
            descriptionEn: 'Information about the team, contact details and privacy.'
        );

        return view('ueber-uns', compact(
            'item', 'locale', 'meta', 'subinfos', 'slugs', 'activeTab'
        ));
    }
}