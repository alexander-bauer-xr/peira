{{-- resources/views/projects/partials/subinfos.blade.php --}}

@php
    // Always fetch any “Subinfo” nodes (field_links → SubinfoItem).
    $subinfos = $item->subinfosFromFieldLinks();

    // Check if this $item is a ProjectItem (otherwise, it’s a RowItem).
    $isProject = $item instanceof \App\Data\ProjectItem;

    // If it’s a project, gather contributors, co-producers, and funders:
    if ($isProject) {
        // “Mitwirkende” list: each element is ['first', 'second', 'third']
        $contributors = $item->contributors();
        // Co-production partners as CoProducerItem[]
        $coproducers = $item->coProducers();
        // Funding partners as CoProducerItem[]
        $funders = $item->funders();
    } else {
        $contributors = [];
        $coproducers  = [];
        $funders      = [];
    }
@endphp

{{-- Only show this entire block if there is at least one Subinfo or at least one contributor --}}
@if(count($subinfos) || count($contributors))
    <div class="linkcontainer">
        {{-- 1) Tabs for each Subinfo --}}
        @foreach($subinfos as $i => $sub)
            <div id="controlinh-{{ $i }}"
                 class="buttonsinfo {{ $i === 0 ? 'activeb' : 'notactiveb' }}">
                {{ $sub->localizedTitle($locale) }}
            </div>
        @endforeach

        {{-- 2) If this is a project and has contributors, add a “Mitwirkende” tab --}}
        @if(count($contributors))
            @php
                $tabIndex = count($subinfos);
            @endphp
            <div id="controlinh-{{ $tabIndex }}" class="buttonsinfo notactiveb">
                {{ __('content.contributors') }}
            </div>
        @endif
    </div>

    <div class="contentwrapper">
        <div class="metalinks">
            {{-- A) Render the body of each Subinfo tab --}}
            @foreach($subinfos as $i => $sub)
                <div id="controledinh-{{ $i }}"
                     class="infosoflinks {{ $i === 0 ? 'disp' : 'nondisp' }}">
                    @replaceVideo($sub->localizedBody($locale))
                </div>
            @endforeach

            {{-- B) Render the “Mitwirkende” tab if applicable --}}
            @if(count($contributors))
                @php
                    $tabIndex = count($subinfos);
                @endphp

                <div id="controledinh-{{ $tabIndex }}" class="infosoflinks nondisp">
                    {{-- B.1) List each contributor --}}
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

                    {{-- B.2) Construct “Co-Produktion zwischen …” sentence if there are co-producers --}}
                    @if(count($coproducers))
                        @php
                            // Build an array of HTML fragments, each either a link or plain name:
                            $cpFragments = array_map(function($cp) {
                                return $cp->asHtmlLink();
                            }, $coproducers);

                            // Join them with commas and “und” before the last item:
                            if(count($cpFragments) === 1) {
                                $coProdString = $cpFragments[0];
                            } else {
                                $last = array_pop($cpFragments);
                                $coProdString = implode(', ', $cpFragments) . ' und ' . $last;
                            }

                            // Similarly for funders:
                            $fFragments = array_map(function($f) {
                                return $f->asHtmlLink();
                            }, $funders);

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
                            {{-- 
                                Example: 
                                "<strong>Projektname</strong> ist eine Kooperation zwischen A und B ... "
                             --}}
                            <strong>{{ $item->localizedTitle($locale) }}</strong>
                            {{ __('content.ist_eine_kooperation_zwischen') }}
                            {!! $coProdString !!}

                            @if(count($funders))
                                {{-- Add “und wird gefördert von” if there are any funders --}}
                                {{ __('content.und_wird_gefoerdert_von') }}
                                {!! $fundString !!}.
                            @else
                                .
                            @endif

                            {{ __('content.alle_inhalte_eigentum') }}
                        </div>
                    @endif

                    {{-- C) If no co-producers exist but there are funders, still show the funding sentence --}}
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

            {{-- D) Render project tags if provided --}}
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

        {{-- E) Finally include the meta‐info block (dates or row meta) --}}
        @include($metainfoView, $metainfoData)
    </div>
@endif