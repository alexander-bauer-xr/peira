@if(count($subinfos))
    <div class="linkcontainer">
        @foreach($subinfos as $slug => $sub)
            <a
                href="/{{ $locale }}/ueber-uns/{{ $slug }}"
                id="controlinh-{{ $loop->index }}"
                class="buttonsinfo {{ $loop->index === $activeTab ? 'activeb' : 'notactiveb' }}"
            >{{ $sub->localizedTitle($locale) }}</a>
        @endforeach
    </div>

    <div class="contentwrapper">
        <div class="metalinks">
            @foreach($subinfos as $slug => $sub)
                <div
                    id="controledinh-{{ $loop->index }}"
                    class="infosoflinks {{ $loop->index === $activeTab ? 'disp' : 'nondisp' }}"
                >{!! $sub->localizedBody($locale) !!}</div>
            @endforeach
        </div>
    </div>
@endif