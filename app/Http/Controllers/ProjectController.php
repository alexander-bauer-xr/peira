<?php

namespace App\Http\Controllers;

use App\Data\MetaData;
use App\Data\ReihungItem;
use App\Services\DrupalApiService;
use Illuminate\Support\Collection;

class ProjectController extends Controller
{
    public function index(DrupalApiService $drupal, $locale = 'de')
    {
        app()->setLocale($locale);

        $reihungRaw = $locale === 'en'
            ? $drupal->getReihungEn()
            : $drupal->getReihung();

        /** @var Collection<int, \App\Data\ReihungItem> $reihenfolge */
        $reihenfolge = collect($reihungRaw)->map(
            fn($item) => \App\Data\ReihungItem::fromDrupal($item)
        );

        $tags = $drupal->getTags();

        $meta = new MetaData(
            title: 'Peira - Projekte',
            titleEn: 'Peira - Projects',
            description: 'Alle Projekte von Peira im Überblick.',
            descriptionEn: 'An overview of all Peira projects.'
        );

        return view('projects.index', [
            'reihenfolge' => $reihenfolge,
            'tags' => $tags,
            'locale' => $locale,
            'meta' => $meta,
        ]);
    }

    public function show($slug, DrupalApiService $drupal, $locale = 'de')
    {
        app()->setLocale($locale);

        // Projekt anhand Slug später hier abrufen
        return view('projects.show', [
            'slug' => $slug,
            'locale' => $locale,
        ]);
    }
}