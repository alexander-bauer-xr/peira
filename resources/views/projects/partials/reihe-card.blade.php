<div class="{{ $reihe->style }} card">
    <a class="{{ $reihe->darkText ? 'blacktextcard' : 'whitetextcard' }}" href="/{{ $locale }}/reihen/{{ $reihe->slug() }}">
        <div class="tagcontainer">
            @foreach ($reihe->tagLabels($locale) as $tag)
                <div class="tag {{ $reihe->darkText ? 'borderblack' : 'borderwhite' }}">
                    {{ $tag }}
                </div>
            @endforeach
        </div>

        <div class="cardtitle h3-text">{{ $reihe->localizedTitle($locale) }}</div>

        @if ($reihe->imageUrl)
            <img loading="lazy" class="image" src="{{ $reihe->imageUrl }}" alt="{{ $reihe->localizedTitle($locale) }}">
        @endif
    </a>
</div>
