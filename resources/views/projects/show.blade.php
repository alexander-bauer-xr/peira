@extends('layouts.app')

@section('content')
    <div id="subpage" class="sub_page scrollbarstyle displayblock">

        @include('partials.social-icons-sub')
        <div class="inner_container_vor">
            <div class="before-margin">
                <div class="nav-flex"><a href="{{ route('projects.index', ['locale' => $locale]) }}">
                        {{ __('content.projects') }}
                    </a>
                    <img id="projekttrenner" alt="{{ __('content.project_separator') }}"
                        src="{{ asset('img/projekttrenner.svg') }}">
                </div>
                <h1 class="ueberschrift h1-text">{{ $project->localizedTitle($locale) }}</h1>
            </div>

            @if ($project->localizedBody($locale))
                <div class="wrapper-vor">
                    <div class="vorangestellt vor-text">
                        {!! $project->localizedBody($locale) !!}
                    </div>
                    @include('projects.partials.gallery', ['images' => $project->images ?? []])
                </div>
            @endif



            @if ($project->yearFormatted())
                <p><strong>Year:</strong> {{ $project->yearFormatted() }}</p>
            @endif

            @if (!empty($project->tags))
                <div class="projecttagsandsocial">
                    <div class="tagsforprojects">
                        @foreach ($project->tagLabels($locale) as $tag)
                            <div class="projecttag">{{ $tag }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/project.js')
@endpush