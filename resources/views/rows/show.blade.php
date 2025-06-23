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
                <img
                  id="projekttrenner"
                  alt="{{ __('content.project_separator') }}"
                  src="{{ asset('img/projekttrenner.svg') }}"
                >
            </div>

            <h1 class="ueberschrift h1-text">
                {{ $row->localizedTitle($locale) }}
            </h1>
        </div>

        @if ($row->localizedBody($locale))
            <div class="wrapper-vor">
                <div class="vorangestellt row-vor vor-text">
                    {!! $row->localizedBody($locale) !!}
                </div>
            </div>
        @endif

        @if ($projects->isNotEmpty())
            @include('rows.partials.row-gallery', [
              'row'      => $row,
              'projects' => $projects,
              'locale'   => $locale,
            ])
        @endif

        <div class="inner_container">
            <div class="content body-text">
                @php
                  $tags          = $tagsProject ?? [];
                  $metainfoArgs  = ['item' => $row, 'locale' => $locale, 'projects' => $projects];
                @endphp

                @include('projects.partials.subinfos', [
                  'item'         => $row,
                  'locale'       => $locale,
                  'tags'         => $tags,
                  'metainfoView' => 'projects.partials.metainfo',
                  'metainfoData' => $metainfoArgs,
                  'subinfos'     => $subinfos,
                  'contributors' => $contributors,
                  'coproducers'  => $coproducers,
                  'funders'      => $funders,
                ])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/pages/rows.js')
@endpush