@extends('layouts.app')

@section('content')
    <div id="subpage" class="sub_page scrollbarstyle displayblock" data-page="{{ $pageKey }}">

        @include('partials.social-icons-sub')

        <div class="inner_container_vor">
            <div class="before-margin">
                <h1 class="ueberschrift h1-text">{{ $pageTitle }}</h1>
            </div>

            @php($introBody = $item->localizedBody($locale))

            <div class="wrapper-vor">
                @if($introBody)
                    <div class="vorangestellt vor-text">
                        @replaceVideo($introBody)
                    </div>
                @endif

                @if($item->imageUrl)
                    <div class="gallerie">
                        <figure id="gallery" class="img-container">
                            <img class="imgcover" alt="{{ $item->title }}" src="{{ $item->imageUrl }}">
                        </figure>
                    </div>
                @endif
            </div>

            <div class="inner_container">
                <div class="content body-text">
                    @include('partials.subinfos-simple', ['routeSegment' => $routeSegment])
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/ueber.js')
@endpush
