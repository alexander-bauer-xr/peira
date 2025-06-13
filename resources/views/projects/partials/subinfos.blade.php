@php
    $activeTab = $activeTab ?? 0;
    $subinfos = $item->subinfosFromFieldLinks();

    $isProject = $item instanceof \App\Data\ProjectItem;

    if ($isProject) {
        $contributors = $item->contributors();
        $coproducers = $item->coProducers();
        $funders = $item->funders();
    } else {
        $contributors = [];
        $coproducers = [];
        $funders = [];
    }
@endphp

@if(count($subinfos) || count($contributors))
    <div class="linkcontainer">
        @foreach($subinfos as $i => $sub)
            <a
                href="{{ $item->url($locale) }}/{{ $i }}"
                id="controlinh-{{ $i }}"
                class="buttonsinfo {{ $i === $activeTab ? 'activeb' : 'notactiveb' }}"
            >{{ $sub->localizedTitle($locale) }}</a>
        @endforeach

        @if(count($contributors))
            @php $tabIndex = count($subinfos); @endphp
            <a
                href="{{ $item->url($locale) }}/{{ $tabIndex }}"
                id="controlinh-{{ $tabIndex }}"
                class="buttonsinfo {{ $tabIndex === $activeTab ? 'activeb' : 'notactiveb' }}"
            >{{ __('content.contributors') }}</a>
        @endif
    </div>

    <div class="contentwrapper">
        <div class="metalinks">
            @foreach($subinfos as $i => $sub)
                <div
                    id="controledinh-{{ $i }}"
                    class="infosoflinks {{ $i === $activeTab ? 'disp' : 'nondisp' }}"
                >@replaceVideo($sub->localizedBody($locale))</div>
            @endforeach

            @if(count($contributors))
                @php $tabIndex = count($subinfos); @endphp

                <div
                    id="controledinh-{{ $tabIndex }}"
                    class="infosoflinks {{ $tabIndex === $activeTab ? 'disp' : 'nondisp' }}"
                >
                    @foreach($contributors as $c)
                        @if(!empty($c['third']))
                            <strong>
                                <a href="{{ $c['third'] }}" target="_blank">
                                    {{ $c['first'] }}
                                </a>
                            </strong>
                            {{ $c['second'] }}<br>
                        @else
                            <strong>{{ $c['first'] }}</strong> {{ $c['second'] }}<br>
                        @endif
                    @endforeach

                    <br><br>

                    @if(count($coproducers))
                        @php
                            $cpFragments = array_map(fn($cp) => $cp->asHtmlLink(), $coproducers);
                            if(count($cpFragments) === 1) {
                                $coProdString = $cpFragments[0];
                            } else {
                                $last = array_pop($cpFragments);
                                $coProdString = implode(', ', $cpFragments) . ' und ' . $last;
                            }

                            $fFragments = array_map(fn($f) => $f->asHtmlLink(), $funders);
                            if(count($fFragments) === 1) {
                                $fundString = $fFragments[0];
                            } elseif(count($fFragments) > 1) {
                                $lastF = array_pop($fFragments);
                                $fundString = implode(', ', $fFragments) . ' und ' . $lastF;
                            } else {
                                $fundString = '';
                            }
                        @endphp

                        <div class="co-production">
                            <strong>{{ $item->localizedTitle($locale) }}</strong>
                            {{ __('content.ist_eine_kooperation_zwischen') }}
                            {!! $coProdString !!}

                            @if(count($funders))
                                {{ __('content.und_wird_gefoerdert_von') }}
                                {!! $fundString !!}.
                            @else
                                .
                            @endif

                            {{ __('content.alle_inhalte_eigentum') }}
                        </div>
                    @endif

                    @if(count($coproducers) === 0 && count($funders))
                        <div class="funding">
                            <strong>{{ $item->localizedTitle($locale) }}</strong>
                            {{ __('content.wird_gefoerdert_von') }}
                            {!! $fundString !!}.
                            {{ __('content.alle_inhalte_eigentum') }}
                        </div>
                    @endif
                </div>
            @endif

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
