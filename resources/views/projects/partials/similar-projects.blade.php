@php
    $currentId = $item->id;
    $tagIds    = $item->tags;
@endphp

<div class="copyright">
  <div class="impressum">
    <h2>{{ __('content.similar_projects') }}</h2>

    <div id="similar-wrapper"
         class="projectcontents"
         data-current-id="{{ $currentId }}"
         data-locale="{{ $locale }}"
         data-tags='@json($tagIds)'>
      <div id="arrowforw">
        <img
          src="{{ asset('img/nav/garrow.svg') }}"
          alt="›"
          id="imgarrowforw"
          class="arrowforwrow"
        >
      </div>

      <div id="arrowback">
        <img
          src="{{ asset('img/nav/garrow.svg') }}"
          alt="‹"
          id="imgarrowback"
          class="arrowbackrow"
        >
      </div>

      <div class="list scrollbarstyletrans" id="inner-wrapper-similar">
      </div>
    </div>
  </div>

  <div class="copyrighttext">
    © {{ date('Y') }} Peira GbR
  </div>
</div>
