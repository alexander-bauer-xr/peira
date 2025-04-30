@php
    $hasGallery = !empty($images);
@endphp

<div class="gallerie">
    <figure id="gallery" data-id="{{ $hasGallery ? -1 : 'static' }}" class="img-container">
        <div id="copyright"></div>

        @if ($hasGallery)
            <div id="arrowforw" onclick="loadImg('up')">
                <img alt="Pfeil" class="arrowforw" src="{{ asset('img/nav/garrow.svg') }}">
            </div>
            <div id="arrowback" onclick="loadImg('down')">
                <img alt="Pfeil" class="arrowback" src="{{ asset('img/nav/garrow.svg') }}">
            </div>
        @endif

        <img class="imgcover"
             alt="{{ $project->localizedTitle($locale) }}"
             src="{{ $project->imageUrl }}">
    </figure>

    @if ($hasGallery)
        <script>
            window.galleryData = {!! json_encode(
                collect($images)->map(fn($img) => [
                    $img['alt'] ?? '',
                    $img['url'],
                    $img['title'] ?? ''
                ]),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) !!};
        </script>
    @endif
</div>