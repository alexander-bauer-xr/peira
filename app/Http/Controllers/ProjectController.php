<?php

namespace App\Http\Controllers;

use App\Data\MetaData;
use App\Data\ReihungItem;
use App\Data\ProjectItem;
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

        $reihenfolge = collect($reihungRaw)->map(
            fn($item) => ReihungItem::fromDrupal($item)
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
    public function show($locale, $slug, DrupalApiService $drupal)
    {
        app()->setLocale($locale);
    
        $projects = collect($drupal->getProjekte())
            ->map(fn($item) => ProjectItem::fromDrupal($item));
    
        $project = $projects->first(fn(ProjectItem $p) => trim($p->slug()) === trim($slug));
    
        if (!$project) {
            abort(404);
        }
    
        $meta = new MetaData(
            title: 'Peira - ' . $project->title,
            titleEn: 'Peira - ' . ($project->titleEn ?? $project->title),
            description: 'Ein Projekt von Peira: ' . $project->title,
            descriptionEn: 'A project by Peira: ' . ($project->titleEn ?? $project->title)
        );
    
        return view('projects.show', [
            'project' => $project,
            'locale' => $locale,
            'meta' => $meta,
        ]);
    }    
}