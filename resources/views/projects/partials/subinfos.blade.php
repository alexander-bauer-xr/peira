@php
    $subinfos = $project->subinfosFromFieldLinks();
@endphp

@if (count($subinfos))
    <div class="linkcontainer">
        @foreach ($subinfos as $i => $sub)
            <div id="controlinh-{{ $i }}" class="buttonsinfo {{ $i === 0 ? 'activeb' : 'notactiveb' }}">
                {{ $sub->localizedTitle($locale) }}
            </div>
        @endforeach
    </div>

    <div class="contentwrapper">
        <div class="metalinks">
            @foreach ($subinfos as $i => $sub)
                <div id="controledinh-{{ $i }}" class="infosoflinks {{ $i === 0 ? 'disp' : 'nondisp' }}">
                    @replaceVideo($sub->localizedBody($locale))
                </div>
            @endforeach

            @if (!empty($tagsproject))
                <div class="projecttagsandsocial">
                    {!! $tagsproject !!}
                </div>
            @endif
        </div>
    </div>
@endif