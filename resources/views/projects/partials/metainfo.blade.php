{{-- resources/views/projects/partials/metainfo.blade.php --}}

@php
  $isProject = method_exists($item, 'dates');
@endphp

<div class="infosprojects">
  @if($isProject)
    {{-- 1) Veröffentlicht (Jahr & Ort) --}}
    @if($item->yearFormatted())
    <div class="termine">
    <div class="newshead small-text">{{ __('content.published') }}</div>
    <span>{{ $item->yearAndPlace() }}</span>
    </div>
    @endif

    {!! $item->socialMedia() !!}

    {{-- 2) Datumsliste mit Show-More/Show-Less-Logik --}}
    @php
    $dates = $item->dates() ?? [];
    $totalDates = is_array($dates) ? count($dates) : 0;
    @endphp

    @if($totalDates > 0)
    <div class="termine">
    <div class="newshead small-text">{{ __('content.dates') }}</div>

    @foreach($dates as $date)
      @if($loop->index < 3)
      <div class="termin">
      <div class="dateproject body-text">{{ $date->formattedDates }}</div>
      <div class="titelproject body-text-bold">
      {{ $date->localizedTitle($locale) }}
      </div>
      <div class="ortproject body-text">
      {!! $date->place->asHtmlLink() !!}
      </div>
      </div>
      @elseif($loop->index === 3)
      <div id="extraDates" class="d-none">
      <div class="termin">
      <div class="dateproject body-text">{{ $date->formattedDates }}</div>
      <div class="titelproject body-text-bold">
      {{ $date->localizedTitle($locale) }}
      </div>
      <div class="ortproject body-text">
      {!! $date->place->asHtmlLink() !!}
      </div>
      </div>
      @else
      <div class="termin">
      <div class="dateproject body-text">{{ $date->formattedDates }}</div>
      <div class="titelproject body-text-bold">
      {{ $date->localizedTitle($locale) }}
      </div>
      <div class="ortproject body-text">
      {!! $date->place->asHtmlLink() !!}
      </div>
      </div>
      @endif
    @endforeach

      @if($totalDates > 3)
    </div>
    <div class="buttonwrapper">
      <button id="toggleButton" class="morebutton" aria-expanded="false" aria-controls="extraDates">
      {{ __('content.show_more') }}
      </button>
    </div>
    @endif
    </div>
    @endif

    {{-- Ko-Produzenten --}}
    @php
    $koproduzenten = $item->coProducers();
    @endphp
    @if(count($koproduzenten))
    <div class="foerderung">
    <div class="foerderunghead small-text">
      In Kooperation mit
    </div>
    <div class="container-fluid py-2 flex-grid-logos">
      @foreach($koproduzenten as $k)
      <div class="col">
      <a href="{{ $k->getLink() }}" target="_blank" title="{{ $k->getName() }}">
      <img src="{{ $k->getLogoUrl() }}" alt="{{ $k->getLogoAlt() ?? $k->getName() }}">
      </a>
      </div>
    @endforeach
    </div>
    </div>
    @endif

    {{-- Förderer --}}
    @php
    $foerderer = $item->funders();
    @endphp
    @if(count($foerderer))
    <div class="foerderung">
    <div class="foerderunghead small-text">
      Dieses Projekt wurde gefördert von
    </div>
    <div class="container-fluid py-2 flex-grid-logos">
      @foreach($foerderer as $f)
      <div class="col">
      <a href="{{ $f->getLink() }}" target="_blank" title="{{ $f->getName() }}">
      <img src="{{ $f->getLogoUrl() }}" alt="{{ $f->getLogoAlt() ?? $f->getName() }}">
      </a>
      </div>
    @endforeach
    </div>
    </div>
    @endif


  @else
    {{-- 3) Für Reihen (Social + Projekte in der Serie) --}}
    {!! $item->socialMedia() !!}
  </div>

  <div class="newshead small-text">
    {{ __('content.projects_in_series') }}
    <br><br>
  </div>
  @foreach($projects as $proj)
    <div class="dateproject body-text rowprojects">
    <a href="{{ $proj->url($locale) }}?row={{ $item->slug() }}">
    {{ $proj->localizedTitle($locale) }}
    </a>
    </div>
  @endforeach
@endif
</div>