@php
    // Prepare row title for query param
    $rowTitle = $row->localizedTitle($locale);
@endphp

<div class="rowwrapper">
    <div class="d-flex flex-row">
        <div id="arrowforw">
            <img alt="Pfeil" id="imgarrowforw" class="arrowforwrow" src="{{ asset('img/nav/garrow.svg') }}">
        </div>

        <div id="arrowback">
            <img alt="Pfeil" id="imgarrowback" class="arrowbackrow" src="{{ asset('img/nav/garrow.svg') }}">
        </div>

        <div class="list scrollbarstyletrans">
            @foreach ($projects as $proj)
                @php
                    $styles = $proj->coverStyles();
                    $imgUrl = $styles['large'] 
                        ?? ($styles['large_16_9'] ?? ($styles['16_9'] ?? (is_array($styles) ? (reset($styles) ?: '') : '')));
                @endphp
                <div class="card item">
                    <a class="rowimg" href="{{ $proj->url($locale) }}?row={{ urlencode($rowTitle) }}">
                        <div class="h3-text titleforimg">{{ $proj->localizedTitle($locale) }}</div>
                        <img class="imagecover" src="{{ $imgUrl }}" alt="{{ $proj->localizedTitle($locale) }}">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
