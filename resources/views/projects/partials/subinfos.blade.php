@php
    $subinfos = $project->subinfosFromFieldLinks();
@endphp

@if (count($subinfos))
    <div class="linkcontainer">
        @foreach ($subinfos as $i => $sub)
            <div id="controlinh-{{ $i }}" class="buttonsinfo {{ $i === 0 ? 'activeb' : 'notactiveb' }}">
                {{ app()->getLocale() === 'en' ? $sub->subtitleEn : ($sub->klarTitle ?? $sub->title) }}
            </div>
        @endforeach
    </div>

    <div class="contentwrapper">
        <div class="metalinks">
            @foreach ($subinfos as $i => $sub)
                <div id="controledinh-{{ $i }}" class="infosoflinks {{ $i === 0 ? 'disp' : 'nondisp' }}">
                    @replaceVideo(app()->getLocale() === 'en' ? $sub->bodyHtmlEn : $sub->bodyHtml)
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