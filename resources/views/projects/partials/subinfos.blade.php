@php
    $activeTab = $activeTab ?? 0;
    $subinfos = $subinfos ?? [];
    $contributors = $contributors ?? [];
    $coproducers = $coproducers ?? [];
    $funders = $funders ?? [];
    $isProject = $item instanceof \App\Data\ProjectItem;
@endphp

@if(count($subinfos) || count($contributors))
    <div class="linkcontainer">
        @foreach($subinfos as $i => $sub)
            <x-a-link href="{{ $item->url($locale) }}/{{ $i }}" id="controlinh-{{ $i }}"
                class="buttonsinfo {{ $i === $activeTab ? 'activeb' : 'notactiveb' }}" label="{{ $sub->localizedTitle($locale) }}">{{ $sub->localizedTitle($locale) }}</x-a-link>
        @endforeach

        @if(count($contributors))
            @php $tabIndex = count($subinfos); @endphp
            <x-a-link href="{{ $item->url($locale) }}/{{ $tabIndex }}" id="controlinh-{{ $tabIndex }}"
                class="buttonsinfo {{ $tabIndex === $activeTab ? 'activeb' : 'notactiveb' }}" label="{{ __('content.contributors') }}">{{ __('content.contributors') }}</x-a-link>
        @endif
    </div>

    <div class="contentwrapper">
        <div class="metalinks">
            @foreach($subinfos as $i => $sub)
                <div id="controledinh-{{ $i }}" class="infosoflinks {{ $i === $activeTab ? 'disp' : 'nondisp' }}">
                    @replaceVideo($sub->localizedBody($locale))
                </div>
            @endforeach

            @if(count($contributors))
                @php $tabIndex = count($subinfos); @endphp

                <div id="controledinh-{{ $tabIndex }}" class="infosoflinks {{ $tabIndex === $activeTab ? 'disp' : 'nondisp' }}">
                    @foreach($contributors as $c)
                        @if(!empty($c['third']))
                            <strong><x-a-link href="{{ $c['third'] }}" target="_blank" label="{{ $c['first'] }}">{{ $c['first'] }}</x-a-link></strong> {{ $c['second'] }}<br>
                        @else
                            <strong>{{ $c['first'] }}</strong> {{ $c['second'] }}<br>
                        @endif
                    @endforeach

                    <br><br>

                    @php
                        $coProdString = collect($coproducers)->map->asHtmlLink()->join(', ', ' und ');
                        $fundString = collect($funders)->map->asHtmlLink()->join(', ', ' und ');
                    @endphp

                    @if($coProdString !== '')
                        <div class="co-production">
                            <strong>{{ $item->localizedTitle($locale) }}</strong>
                            {{ __('content.ist_eine_kooperation_zwischen') }}
                            {!! $coProdString !!}
                            @if($fundString !== '')
                                {{ __('content.und_wird_gefoerdert_von') }}
                                {!! $fundString !!}.
                            @else
                                .
                            @endif
                            {{ __('content.alle_inhalte_eigentum') }}
                        </div>
                    @elseif($fundString !== '')
                        <div class="funding">
                            <strong>{{ $item->localizedTitle($locale) }}</strong>
                            {{ __('content.ist_eine_produktion_von') }} Peira GbR
                            {{ __('content.und_wird_gefoerdert_von') }}
                            {!! $fundString !!}.
                            {{ __('content.alle_inhalte_eigentum') }}
                        </div>
                    @else
                        <div class="funding">
                            <strong>{{ $item->localizedTitle($locale) }}</strong>
                            {{ __('content.ist_eine_produktion_von') }} Peira GbR.
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