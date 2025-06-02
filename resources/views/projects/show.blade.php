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
                        <img id="projekttrenner" alt="{{ __('content.project_separator') }}"
                            src="{{ asset('img/projekttrenner.svg') }}">
                        <a href="{{ $reihe->url($locale) }}">{{ $reihe->localizedTitle($locale) }}</a>
                    @endif

                    <img id="projekttrenner" alt="{{ __('content.project_separator') }}"
                        src="{{ asset('img/projekttrenner.svg') }}">
                    <span>{{ $project->localizedTitle($locale) }}</span>
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

            <div class="infosprojects">
                @if ($project->yearFormatted())

                    <div class="termine">
                        <div class="newshead small-text">{{ __('content.published') }}</div>
                        <span>{{ $project->yearAndPlace() }}</span>
                    </div>
                @endif

                @php $dates = $project->dates(); @endphp

                @if ($project->dates())
                    <div class="termine">
                        <div class="newshead small-text">{{ __('content.dates') }}</div>

                        @foreach ($project->dates() as $date)
                            <div class="termin">
                                <div class="dateproject body-text">{{ $date->formattedDates }}</div>
                                <div class="titelproject body-text-bold">{{ $date->localizedTitle($locale) }}</div>
                                <div class="ortproject body-text">{!! $date->place->asHtmlLink() !!}</div>
                            </div>
                        @endforeach
                    </div>
                @endif


            </div>

            @include('projects.partials.subinfos', ['project' => $project, 'tagsproject' => $tagsProject ?? '', 'locale' => $locale])

        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/project.js')
@endpush