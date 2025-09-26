{{-- resources/views/projects/partials/metainfo.blade.php --}}

@php
    $isProject = method_exists($item, 'dates');
@endphp

<div class="infosprojects">
    @if ($isProject)
        @if ($item->yearFormatted())
            <div class="termine">
                <div class="newshead small-text">{{ __('content.published') }}</div>
                <span>{{ $item->yearAndPlace() }}</span>
            </div>
        @endif

        {!! $item->socialMedia(app(App\Services\SocialMediaRenderer::class)) !!}

        @php
            $api = app(App\Services\DrupalApiService::class);
            $dates = collect($item->dates($api) ?? [])->values();
            $first = $dates->take(3);
            $rest  = $dates->slice(3);
        @endphp

        @if ($dates->isNotEmpty())
            <div class="termine">
                <div class="newshead small-text">{{ __('content.dates') }}</div>

                @foreach ($first as $date)
                    <div class="termin">
                        <div class="dateproject body-text">{{ $date->formattedDates }}</div>
                        <div class="titelproject body-text-bold">{{ $date->localizedTitle($locale) }}</div>
                        <div class="ortproject body-text">{!! $date->place->asHtmlLink() !!}</div>
                    </div>
                @endforeach

                @if ($rest->isNotEmpty())
                    <div id="extraDates" class="d-none">
                        @foreach ($rest as $date)
                            <div class="termin">
                                <div class="dateproject body-text">{{ $date->formattedDates }}</div>
                                <div class="titelproject body-text-bold">{{ $date->localizedTitle($locale) }}</div>
                                <div class="ortproject body-text">{!! $date->place->asHtmlLink() !!}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($rest->isNotEmpty())
                <div class="buttonwrapper">
                    <button id="toggleButton" class="morebutton" aria-expanded="false" aria-controls="extraDates">
                        {{ __('content.show_more') }}
                    </button>
                </div>
            @endif
        @endif

        @php
            $koproduzenten = $item->coProducers($api) ?? [];
        @endphp
        @if (!empty($koproduzenten))
            <div class="foerderung">
                <div class="foerderunghead small-text">In Kooperation mit</div>
                <div class="container-fluid py-2 flex-grid-logos">
                    @foreach ($koproduzenten as $k)
                        <div class="col">
                            <x-a-link href="{{ $k->getLink() }}" external target="_blank" label="{{ $k->getName() }}">
                                <img src="{{ $k->getLogoUrl() }}" alt="{{ $k->getLogoAlt() ?? $k->getName() }}">
                            </x-a-link>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @php
            $foerderer = $item->funders($api) ?? [];
        @endphp
        @if (!empty($foerderer))
            <div class="foerderung">
                <div class="foerderunghead small-text">Dieses Projekt wurde gefördert von</div>
                <div class="container-fluid py-2 flex-grid-logos">
                    @foreach ($foerderer as $f)
                        <div class="col">
                            <x-a-link href="{{ $f->getLink() }}" external target="_blank" label="{{ $f->getName() }}">
                                <img src="{{ $f->getLogoUrl() }}" alt="{{ $f->getLogoAlt() ?? $f->getName() }}">
                            </x-a-link>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        {!! $item->socialMedia(app(App\Services\SocialMediaRenderer::class)) !!}

        <div class="newshead small-text">
            {{ __('content.projects_in_series') }}
            <br><br>
        </div>

        @foreach (($projects ?? []) as $proj)
            <div class="dateproject body-text rowprojects">
                <x-a-link href="{{ $proj->url($locale) }}" label="{{ $proj->localizedTitle($locale) }}">
                    {{ $proj->localizedTitle($locale) }}
                </x-a-link>
            </div>
        @endforeach
    @endif
</div>
