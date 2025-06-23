@extends('layouts.app')

@section('content')
<div id="subpage" class="sub_page scrollbarstyle displayblock">

    @include('partials.social-icons-sub')

    <div class="inner_container_vor">
        <div class="before-margin">
            <div class="nav-flex">
                <a href="{{ route('projects.index', ['locale' => $locale]) }}">
                    {{ __('content.projects') }}
                </a>

                @if ($reihe = $project->reihe())
                    <img
                        id="projekttrenner"
                        alt="{{ __('content.project_separator') }}"
                        src="{{ asset('img/projekttrenner.svg') }}"
                    >
                    <a href="{{ $reihe->url($locale) }}">
                        {{ $reihe->localizedTitle($locale) }}
                    </a>
                @endif

                <img
                    id="projekttrenner"
                    alt="{{ __('content.project_separator') }}"
                    src="{{ asset('img/projekttrenner.svg') }}"
                >
                <span>{{ $project->localizedTitle($locale) }}</span>
            </div>

            <h1 class="ueberschrift h1-text">
                {{ $project->localizedTitle($locale) }}
            </h1>
        </div>

        @if($project->localizedBody($locale))
            <div class="wrapper-vor">
                <div class="vorangestellt vor-text">
                    {!! $project->localizedBody($locale) !!}
                </div>

                @include('projects.partials.gallery', [
                    'images' => $project->images ?? []
                ])
            </div>
        @endif
    </div>

    <div class="inner_container">
        <div class="content body-text">
            @php
                $tags          = $tagsProject ?? [];
                $metainfoData  = ['item' => $project, 'locale' => $locale];
            @endphp

            @include('projects.partials.subinfos', [
                'item'         => $project,
                'locale'       => $locale,
                'tags'         => $tags,
                'metainfoView' => 'projects.partials.metainfo',
                'metainfoData' => $metainfoData,
                'subinfos'     => $subinfos,
                'contributors' => $contrib,
                'coproducers'  => $coproducers,
                'funders'      => $funders,
            ])
        </div>
    </div>
    @include('projects.partials.similar-projects', [
    'item'   => $project,
    'locale' => $locale,
    ])
</div>
@endsection

@push('scripts')
    @vite('resources/js/pages/project.js')
@endpush