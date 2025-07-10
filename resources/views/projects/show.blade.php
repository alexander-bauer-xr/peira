@extends('layouts.app')

@section('content')
    <div id="subpage" class="sub_page scrollbarstyle displayblock">

        @include('partials.social-icons-sub')

        <div class="inner_container_vor">
            <div class="before-margin">
                <div class="nav-flex">
                    <x-a-link href="{{ route('projects.index', ['locale' => $locale]) }}"
                        label="{{ __('content.projects') }}">
                        {{ __('content.projects') }}
                    </x-a-link>

                    @if ($reihe = $project->reihe(app(App\Services\DrupalApiService::class)))
                        <img id="projekttrenner" alt="{{ __('content.project_separator') }}"
                            src="{{ asset('img/projekttrenner.svg') }}">
                        <x-a-link href="{{ $reihe->url($locale) }}" label="{{ $reihe->localizedTitle($locale) }}">
                            {{ $reihe->localizedTitle($locale) }}
                        </x-a-link>
                    @endif

                    <img id="projekttrenner" alt="{{ __('content.project_separator') }}"
                        src="{{ asset('img/projekttrenner.svg') }}">
                </div>

                <h1 class="ueberschrift h1-text">
                    {{ $project->localizedTitle($locale) }}
                </h1>
            </div>

            @if ($project->localizedBody($locale))
                <div class="wrapper-vor">
                    <div class="vorangestellt vor-text">
                        {!! $project->localizedBody($locale) !!}
                    </div>

                    @include('projects.partials.gallery', [
                        'project' => $project,
                        'images' => $images,
                        'coverImage' => $coverImage,
                        'coverStyles' => $coverStyles,
                        'locale' => $locale,
                    ])
                </div>
            @endif
        </div>

        <div class="inner_container">
            <div class="content body-text">
                @php
                    $tags = $tagsProject ?? [];
                    $metainfoData = ['item' => $project, 'locale' => $locale];
                @endphp

                @include('projects.partials.subinfos', [
                    'item' => $project,
                    'locale' => $locale,
                    'tags' => $tags,
                    'metainfoView' => 'projects.partials.metainfo',
                    'metainfoData' => $metainfoData,
                    'subinfos' => $subinfos,
                    'contributors' => $contributors,
                    'coproducers' => $coproducers,
                    'funders' => $funders,
                ])
            </div>
        </div>
        @include('projects.partials.similar-projects', [
            'item' => $project,
            'locale' => $locale,
        ])
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/project.js')
@endpush
