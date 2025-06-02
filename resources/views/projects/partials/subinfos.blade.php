@php
 $subinfos = $item->subinfosFromFieldLinks();
@endphp

@if(count($subinfos))
  <div class="linkcontainer">
    @foreach($subinfos as $i => $sub)
      <div id="controlinh-{{ $i }}" class="buttonsinfo {{ $i === 0 ? 'activeb' : 'notactiveb' }}">
        {{ $sub->localizedTitle($locale) }}
      </div>
    @endforeach
  </div>

  <div class="contentwrapper">
    <div class="metalinks">
      @foreach($subinfos as $i => $sub)
        <div id="controledinh-{{ $i }}" class="infosoflinks {{ $i === 0 ? 'disp' : 'nondisp' }}">
          @replaceVideo($sub->localizedBody($locale))
        </div>
      @endforeach

      @if(!empty($tags))
        <div class="projecttagsandsocial">
          <div class="tagsforprojects">
            @foreach($tags as $tag)
              <div class="projecttag">{{ $tag }}</div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    @include($metainfoView, $metainfoData)
  </div>
@endif
