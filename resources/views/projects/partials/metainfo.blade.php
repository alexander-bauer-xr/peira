@php
  $isProject = method_exists($item, 'dates');
@endphp

<div class="infosprojects">
  @if($isProject)
    @if($item->yearFormatted())
      <div class="termine">
        <div class="newshead small-text">{{ __('content.published') }}</div>
        <span>{{ $item->yearAndPlace() }}</span>
      </div>
    @endif

    @if($item->dates())
      <div class="termine">
        <div class="newshead small-text">{{ __('content.dates') }}</div>
        @foreach($item->dates() as $date)
          <div class="termin">
            <div class="dateproject body-text">{{ $date->formattedDates }}</div>
            <div class="titelproject body-text-bold">
              {{ $date->localizedTitle($locale) }}
            </div>
            <div class="ortproject body-text">
              {!! $date->place->asHtmlLink() !!}
            </div>
          </div>
        @endforeach
      </div>
    @endif

  @else
    <div class="termine">
      <div class="newshead small-text">{{ __('content.social') }}</div>
      <div class="social-media-grid">
        @if(!empty($item->raw['field_social_instagram']))
          <div class="social-media-row d-flex flex-row gap-2">
            <div class="social-media-first">IG</div>
            <a
              href="{{ $item->raw['field_social_instagram'][0]['uri'] }}"
              class="social-media-second"
              target="_blank"
            >
              {{ $item->raw['field_social_instagram'][0]['title'] }}
            </a>
          </div>
        @endif
      </div>
    </div>

    <div class="newshead small-text">
      {{ __('content.projects_in_series') }}<br><br>
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