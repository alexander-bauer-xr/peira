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
    public function show($locale, $slug, DrupalApiService $drupal, int $tabIndex = 0)
    {
        app()->setLocale($locale);

        $projects = collect($drupal->getProjekte())
            ->map(fn($item) => ProjectItem::fromDrupal($item));

        $project = $projects->first(callback: fn(ProjectItem $p) => trim($p->slug()) === trim($slug));

        if (!$project) {
            abort(404);
        }

        $meta = new MetaData(
            title: 'Peira - ' . $project->title,
            titleEn: 'Peira - ' . ($project->titleEn ?? $project->title),
            description: 'Ein Projekt von Peira: ' . $project->title,
            descriptionEn: 'A project by Peira: ' . ($project->titleEn ?? $project->title)
        );

        $images = collect($project->images)
            ->map(function (array $g) use ($drupal) {
                $styles = data_get(
                    $drupal->getFileByUuid($g['target_uuid']),
                    'data.attributes.image_style_uri',
                    []
                );
                return [
                    'alt' => $g['alt'] ?? '',
                    'title' => $g['title'] ?? '',
                    'styles' => $styles,
                ];
            })->values();

        $coverRaw = $project->raw['field_titelbild'][0] ?? [];
        // Titelbild
        $coverStyles = $project->imageUuid()
            ? ($drupal->getFileByUuid($project->imageUuid())['data']['attributes']['image_style_uri'] ?? [])
            : [];
        $cover = [
            'uuid' => $project->imageUuid(),
            'alt' => $coverRaw['alt'] ?? $project->localizedTitle($locale),
            'title' => $coverRaw['title'] ?? $project->localizedTitle($locale),
            'styles' => $coverStyles,
        ];

        $subinfos = $project->subinfosFromFieldLinks($drupal);
        $subInfoCount = count($subinfos);
        $contrib = $project->contributors();
        $hasContrib = count($contrib) ? 1 : 0;
        $maxIndex = $subInfoCount + $hasContrib;
        $activeTab = min(
            max($tabIndex, 0),
            max($maxIndex, 0)
        );

        $coproducers = $project->coProducers($drupal);
        $funders = $project->funders($drupal);
        $tagsProject = $project->tagLabels($locale, $drupal);

        return view('projects.show', [
            'project' => $project,
            'locale' => $locale,
            'meta' => $meta,
            'tagsProject' => $tagsProject,
            'activeTab' => $activeTab,
            'subinfos' => $subinfos,
            'contributors' => $contrib,
            'coproducers' => $coproducers,
            'funders' => $funders,
            'images' => $images,
            'coverStyles' => $coverStyles,
            'coverImage' => $cover,
        ]);
    }
}