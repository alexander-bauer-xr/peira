<div class="{{ $project->style }} {{ $project->overlay ? 'card' : 'cardo' }}">
    <a class="{{ $project->darkText ? 'blacktextcard' : 'whitetextcard' }}"
        href="/{{ $locale }}/projekte/{{ $project->slug() }}">
        <div class="tagcontainer">
            @if ($project->yearFormatted())
                <div class="tag {{ $project->darkText ? 'borderblack' : 'borderwhite' }}">
                    {{ $project->yearFormatted() }}
                </div>
            @endif

            @foreach ($project->tagLabels($locale, app(App\Services\DrupalApiService::class)) as $tagLabel)
                <div class="tag {{ $project->darkText ? 'borderblack' : 'borderwhite' }}">
                    {{ $tagLabel }}
                </div>
            @endforeach
        </div>

        <div class="cardtitle h3-text">{{ $project->localizedTitle($locale) }}</div>

        @isset($coverStyles)
            <x-responsive-image class="image" :styles="$coverStyles" :alt="$project->localizedTitle($locale)" />
        @endisset
    </a>
</div>
