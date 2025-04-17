<?php

namespace App\Http\Controllers;

use App\Data\MetaData;
use Illuminate\Support\Facades\Http;
use App\Data\NewsItem;
use App\Services\DrupalApiService;

class HomeController extends Controller
{
    public function index(DrupalApiService $drupal, $locale = 'de')
    {
        app()->setLocale($locale); // sets current app locale

        $newsRaw = $drupal->getNews();

        $newsItems = collect($newsRaw)->map(fn($n) => NewsItem::fromDrupal($n));

        $meta = new MetaData(
            title: 'Peira - Kollaboration als machbare Utopie, Kunst als hinterfragende Praxis.',
            titleEn: 'Peira - Collaboration as a feasible utopia, art as a critical practice.',
            description: 'Kollaboration als machbare Utopie, Kunst als hinterfragende Praxis.',
            descriptionEn: 'Collaboration as a feasible utopia, art as a critical practice.'
        );

        return view('home', [
            'newsItems' => $newsItems,
            'locale' => $locale,
            'meta' => $meta,
        ]);
    }
}