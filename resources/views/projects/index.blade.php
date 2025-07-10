@extends('layouts.app')

@section('content')
    <div id="subpage" class="sub_page_proj">


        @include('partials.social-icons-sub')
        <div class="inner_container_vor">

            <div class="before-margin">
                <h1 class="ueberschrift h1-text">{{ __('content.projects') }}</h1>
            </div>
        </div>
        <div class="inner_container">
            <div class="linkcontainer">
                <div id="filterbtn" class="buttonsinfofilter notactiveb">
                    {{ __('content.filter') }}
                    <img id="arrowimg" src="/img/rotpfeil.png">
                </div>

                <div id="listoffilters" class="filterlist hidden">
                    @foreach ($tags as $tag)
                        <div class="buttonsinfofilter activeb marked" data-id="{{ $tag['tid'][0]['value'] }}">
                            {{ $locale === 'en' ? $tag['field_eng'][0]['value'] ?? '' : $tag['name'][0]['value'] ?? '' }}
                        </div>
                    @endforeach
                </div>

                <div id="loadingcard" class="loadingcontainer"></div>
            </div>

            @php
                $drupal = app(\App\Services\DrupalApiService::class);
            @endphp

            <div id="projectgrid" class="grid-wrapper">
                @foreach ($reihenfolge as $item)
                    @php
                        $entries = $item->entries($locale, $drupal);
                    @endphp

                    @foreach ($entries as $entry)
                        @if ($entry instanceof \App\Data\ProjectItem)
                            @php
                                if ($entry->imageUuid()) {
                                    $file = $drupal->getFileByUuid($entry->imageUuid());
                                    $coverStyles = data_get($file, 'data.attributes.image_style_uri', []);
                                } else {
                                    $coverStyles = [];
                                }
                            @endphp

                            @include('projects.partials.card', [
                                'project' => $entry,
                                'locale' => $locale,
                                'coverStyles' => $coverStyles,
                            ])
                        @elseif ($entry instanceof \App\Data\ZitatItem)
                            @include('projects.partials.zitat-card', [
                                'zitat' => $entry,
                                'locale' => $locale,
                            ])
                        @elseif ($entry instanceof \App\Data\RowItem)
                            @include('projects.partials.reihe-card', [
                                'reihe' => $entry,
                                'locale' => $locale,
                            ])
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/helpers/filterProjects.js')
    @endpush
@endsection
