@extends('layouts.app')

@php
    use Illuminate\Support\Carbon;

    $grouped = $newsItems->groupBy(function ($item) {
        return Carbon::parse($item->date)->format('Y-m'); // example: 2025-04
    });
@endphp

@section('title', $locale === 'en' ? $meta->titleEn : $meta->title)

@section('content')
    <div id="subpage" class="sub_page">
        <div class="inner_container">
            <h1 class="ueberschrift h1-text">
                News
            </h1>

            <div class="content body-text">
                @php $currentYear = null; @endphp

                @foreach ($grouped as $yearMonth => $items)
                    @php
                        [$year, $month] = explode('-', $yearMonth);
                        $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('F');
                    @endphp

                    @if ($year !== $currentYear)
                        <h2 class="h3-text red-text mt-10">{{ $year }}</h2>
                        @php $currentYear = $year; @endphp
                    @endif
                    <hr>
                    <h3 class="h4-text black-text mt-4 mb-2">{{ $monthName }}</h3>

                    @foreach ($items as $item)
                        <article class="news-entry mb-6 pb-4 border-b">
                            <h4 class="ueberschrift h3-text">
                                {{ $item->localizedTitle($locale) }}
                            </h4>

                            @if($item->showDate && $item->date)
                                <p class="btext-sm">
                                    {{ \Carbon\Carbon::parse($item->date)->format($item->showTime ? 'd.m.Y – H:i' : 'd.m.Y') }}
                                </p>
                            @endif

                            <div class="newsdetail">
                                {!! $item->localizedBody($locale) !!}
                            </div>
                        </article>
                    @endforeach
                @endforeach
            </div>
        </div>
        @include('partials.footer')
    </div>
@endsection
