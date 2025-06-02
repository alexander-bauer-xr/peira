@extends('layouts.app')

@section('content')
<div id="subpage" class="sub_page scrollbarstyle displayblock">

    {{-- social icons / back to overview --}}
    @include('partials.social-icons-sub')

    <div class="inner_container_vor">
        <div class="before-margin">
            <div class="nav-flex">
                <a href="{{ route('projects.index', ['locale' => $locale]) }}">
                    {{ __('content.projects') }}
                </a>
                <img
                  id="projekttrenner"
                  alt="{{ __('content.project_separator') }}"
                  src="{{ asset('img/projekttrenner.svg') }}"
                >
            </div>

            {{-- row title --}}
            <h1 class="ueberschrift h1-text">
                {{ $row->localizedTitle($locale) }}
            </h1>
        </div>

        {{-- row intro/body --}}
        @if ($row->localizedBody($locale))
            <div class="wrapper-vor">
                <div class="vorangestellt row-vor vor-text">
                    {!! $row->localizedBody($locale) !!}
                </div>
            </div>
        @endif

        {{-- gallery of projects in this row --}}
        @if ($projects->isNotEmpty())
            <div class="rowwrapper">
                <div class="d-flex flex-row">
                    <div id="arrowforw">
                        <img
                          src="{{ asset('img/nav/garrow.svg') }}"
                          id="imgarrowforw"
                          class="arrowforwrow"
                          alt="›"
                        >
                    </div>
                    <div id="arrowback">
                        <img
                          src="{{ asset('img/nav/garrow.svg') }}"
                          id="imgarrowback"
                          class="arrowbackrow"
                          alt="‹"
                        >
                    </div>

                    <div class="list scrollbarstyletrans">
                        @foreach($projects as $proj)
                            <div class="card item">
                                <a
                                  href="{{ $proj->url($locale) }}?row={{ $row->slug() }}"
                                  class="rowimg"
                                >
                                    <div class="h3-text titleforimg">
                                        {{ $proj->localizedTitle($locale) }}
                                    </div>
                                    <img
                                      src="{{ $proj->imageUrl }}"
                                      alt="{{ $proj->localizedTitle($locale) }}"
                                      class="imagecover"
                                    >
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Info / Presse tabs, tags & metainfo --}}
        <div class="inner_container">
            <div class="content body-text">
                @php
                  $tags          = $row->tagLabels($locale);
                  $metainfoArgs  = ['item' => $row, 'locale' => $locale, 'projects' => $projects];
                @endphp

                @include('projects.partials.subinfos', [
                  'item'         => $row,
                  'locale'       => $locale,
                  'tags'         => $tags,
                  'metainfoView' => 'projects.partials.metainfo',
                  'metainfoData' => $metainfoArgs,
                ])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/pages/project.js')
@endpush