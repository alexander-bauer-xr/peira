@php
    $hasGallery = $images->isNotEmpty();
@endphp

<div class="gallerie">
    <figure id="gallery" data-id="{{ $hasGallery ? 0 : 'static' }}" class="img-container">
        <div id="copyright"></div>

        @if ($hasGallery)
            <div id="arrowforw" onclick="loadImg('up')">
                <img class="arrowforw" src="{{ asset('img/nav/garrow.svg') }}" alt="Pfeil">
            </div>
            <div id="arrowback" onclick="loadImg('down')">
                <img class="arrowback" src="{{ asset('img/nav/garrow.svg') }}" alt="Pfeil">
            </div>
        @endif

        <x-responsive-image class="imgcover" :styles="$coverStyles" :alt="$project->localizedTitle($locale)" />

    </figure>
</div>

@if ($hasGallery)
    <script>
        window.galleryData = {!! $images->toJson(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
    </script>
@endif
