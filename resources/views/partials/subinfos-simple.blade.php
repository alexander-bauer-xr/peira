@php($infoBasePath = ($routeSegment ?? 'ueber-uns'))

@if(count($subinfos))
    <div class="linkcontainer">
        @foreach($subinfos as $slug => $sub)
            <x-a-link
                href="{{ url($locale . '/' . $infoBasePath . '/' . $slug) }}"
                id="controlinh-{{ $loop->index }}"
                class="buttonsinfo {{ $loop->index === $activeTab ? 'activeb' : 'notactiveb' }}"
                label="{{ $sub->localizedTitle($locale) }}"
            >
                {{ $sub->localizedTitle($locale) }}
            </x-a-link>
        @endforeach
    </div>

    <div class="contentwrapper">
        <div class="metalinks">
            @foreach($subinfos as $slug => $sub)
                <div
                    id="controledinh-{{ $loop->index }}"
                    class="infosoflinks {{ $loop->index === $activeTab ? 'disp' : 'nondisp' }}"
                >
                    @replaceVideo($sub->localizedBody($locale))
                </div>
            @endforeach
        </div>
    </div>
@endif
