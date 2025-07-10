<div class="{{ $reihe->style }} card">
    <a class="{{ $reihe->darkText ? 'whitetextcard' : 'whitetextcard' }}" href="/{{ $locale }}/reihen/{{ $reihe->slug() }}">
        <div class="tagcontainer">
            @foreach ($reihe->tagLabels($locale, app(App\Services\DrupalApiService::class)) as $tag)
                <div class="tag {{ $reihe->darkText ? 'borderwhite' : 'borderwhite' }}">
                    {{ $tag }}
                </div>
            @endforeach
        </div>

        <div class="cardtitle h3-text">{{ $reihe->localizedTitle($locale) }}</div>

        @isset($coverStyles)
            <x-responsive-image class="image" :styles="$coverStyles" :alt="$reihe->localizedTitle($locale)" />
        @endisset
    </a>
</div>
 