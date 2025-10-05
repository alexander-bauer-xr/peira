@extends('layouts.app')

@section('content')
    <div id="subpage" class="sub_page scrollbarstyle displayblock">

        @include('partials.social-icons-sub')

        <div class="inner_container_vor">
            <div class="before-margin">
                <h2 class="ueberschrift h1-text">{{ $locale === 'en' ? 'About us' : 'Über uns' }}</h2>
            </div>

            <div class="wrapper-vor">
                @if($item->bodyHtml)
                    <div class="vorangestellt vor-text">
                        @replaceVideo($item->localizedBody($locale))
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
                    @include('partials.subinfos-simple')
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/ueber.js')
@endpush