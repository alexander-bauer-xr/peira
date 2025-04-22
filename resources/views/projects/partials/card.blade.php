<div class="{{ $project->style }} {{ $project->overlay ? 'card' : 'cardo' }}">
    <a class="{{ $project->darkText ? 'blacktextcard' : 'whitetextcard' }}" href="/{{ $locale }}/projekte/{{ $project->slug() }}">
        <div class="tagcontainer">
            @if ($project->yearFormatted())
                <div class="tag {{ $project->darkText ? 'borderblack' : 'borderwhite' }}">
                    {{ $project->yearFormatted() }}
                </div>
            @endif

            @foreach($project->tagLabels($locale) as $tagLabel)
                <div class="tag {{ $project->darkText ? 'borderblack' : 'borderwhite' }}">
                    {{ $tagLabel }}
                </div>
            @endforeach
        </div>

        <div class="cardtitle h3-text">{{ $project->localizedTitle($locale) }}</div>

        @if($project->imageUrl)
            <img loading="lazy" class="image" src="{{ $project->imageUrl }}" alt="{{ $project->localizedTitle($locale) }}">
        @endif
    </a>
</div>