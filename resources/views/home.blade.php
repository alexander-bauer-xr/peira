@extends('layouts.app')

@section('content')
    {{-- Logo & Slogan --}}
    <div id="schrift" class="bg">
        <div class="flex-container">
            <div id="logo-container" class="logocontainer">
                <img src="{{ asset('img/peira.svg') }}" alt="logo" id="logo" class="logo">
                <h1>Peira</h1>
                <hr>
                <div id="slogan">
                    <h2 class="h3-text white-text">
                        {{ __('content.collaboration') }}
                    </h2>
                </div>
            </div>

            <div id="newslink">News</div>

            {{-- News Section --}}
            <div id="news" class="scrollbarstyle">
                @foreach ($newsItems as $index => $item)
                    <hr>
                    <div id="news-{{ $index + 1 }}" class="newsitem body-text">
                        <div class="h3-news-container">
                            <div class="headline">
                                <h3 class="h3-text">{{ $item->localizedTitle($locale) }}</h3>
                            </div>
                            <img id="news-button-{{ $index + 1 }}" alt="Toggle News"
                                src="{{ asset('img/nav/Arrow_open_red.png') }}">
                        </div>

                        <div id="news-detail-{{ $index + 1 }}" class="newsdetail out">
                            @if ($item->showDate && $item->date)
                                <div class="date">
                                    {{ \Carbon\Carbon::parse($item->date)->format($item->showTime ? 'd.m. H:i' : 'd.m.') }}
                                </div>
                            @endif
                            <div>{!! $item->localizedBody($locale) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="/{{ $locale }}/news-archiv" class="archive_link">{{ __('content.archiv_link') }}</a>
        </div>
    </div>

    @include('partials.social-icons')
    @include('partials.newsletter-form')

    <div id="overlay" class="bg">
    </div>
    {{-- Fallback background --}}
    <div class="bg">
        <img id="webpPlayer" src="{{ asset('img/seq/000.webp') }}" alt="Peira Video" style="display: none;">
        <img src="{{ asset('img/bg.jpg') }}" alt="background" id="bgimg" class="bgimg">
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/home.js')
@endpush