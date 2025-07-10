@php
    $slug = $row->slug();
    $containerId = "list-{$slug}";
    $forwardId = "arrowforw-{$slug}";
    $backId = "arrowback-{$slug}";
@endphp

<div class="rowwrapper">
    <div class="d-flex flex-row">
        <div id="{{ $forwardId }}">
            <img src="{{ asset('img/nav/garrow.svg') }}" id="imgarrowforw-{{ $slug }}" class="arrowforwrow"
                alt="›">
        </div>

        <div id="{{ $backId }}">
            <img src="{{ asset('img/nav/garrow.svg') }}" id="imgarrowback-{{ $slug }}" class="arrowbackrow"
                alt="‹">
        </div>

        <div id="{{ $containerId }}" class="list scrollbarstyletrans">
            @foreach ($projects as $proj)
                <div class="card item">
                    <x-a-link href="{{ $proj->url($locale) }}" class="rowimg"
                        label="{{ $proj->localizedTitle($locale) }}">
                        <div class="h3-text titleforimg">
                            {{ $proj->localizedTitle($locale) }}
                        </div>
                        
                        <x-responsive-image class="imagecover" :styles="$proj->coverStyles()" :alt="$proj->localizedTitle($locale)" />

                    </x-a-link>
                </div>
            @endforeach
        </div>
    </div>
</div>
