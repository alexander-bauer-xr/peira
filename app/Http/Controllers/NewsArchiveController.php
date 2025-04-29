<?php

namespace App\Http\Controllers;

use App\Services\DrupalApiService;
use App\Data\NewsItem;
use App\Data\MetaData;
use Illuminate\Support\Carbon;

class NewsArchiveController extends Controller
{
    public function index($locale, DrupalApiService $drupal)
    {
        app()->setLocale($locale);

        $newsRaw = $drupal->getNews();

        $newsItems = collect($newsRaw)
            ->map(fn($n) => NewsItem::fromDrupal($n))
            ->filter(function (NewsItem $n) use ($locale) {
                if ($locale === 'en') {
                    // Check if English fields exist
                    return !empty($n->titleEn) && !empty($n->bodyHtmlEn);
                }
                return $n->lang === 'de';
            })
            ->sortByDesc(fn(NewsItem $n) => $n->date ? Carbon::parse($n->date) : Carbon::now());

        $meta = new MetaData(
            title: 'News-Archiv - Peira',
            titleEn: 'News Archive - Peira',
            description: 'Alle Neuigkeiten und Meldungen im Überblick.',
            descriptionEn: 'All news and announcements at a glance.'
        );

        return view('news.archive', [
            'newsItems' => $newsItems,
            'locale' => $locale,
            'meta' => $meta,
        ]);
    }
}
