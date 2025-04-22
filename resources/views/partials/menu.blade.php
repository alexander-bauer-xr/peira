<div class="close_img">
    <div id="menu-interaction"><h4>Menu</h4></div>
    <img id="mobileMenu" class="close_img_mobile" src="{{ asset('img/burger-menu.svg') }}" alt="Peira Menu">
    <img id="webpPlayerMenu" class="close_img" src="{{ asset('img/burger-menu.svg') }}" alt="Peira Menu"
        style="display: none;">
</div>

<div id="clickaway"></div>

<div id="menusvg" class="beginright"><svg width="520" height="471" viewBox="0 0 520 471" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path id="menupath"
            d="M675.938 421.966C617.195 450.393 556.643 458.353 498.202 434.229C426.75 404.725 363.086 424.304 297.942 450.662C213.439 484.863 134.857 475.956 70.653 408.734C4.6794 339.566 4.01127 258.472 40.9378 174.61C65.3205 119.131 65.6213 67.1172 30.6031 14.0568C-18.2833 -59.9473 -5.71393 -167.754 55.6581 -236.987C117.247 -306.596 219.748 -329.962 301.136 -293.691C373.117 -261.762 415.72 -206.681 434.069 -130.852C456.396 -38.5851 475.401 -23.2762 569.449 -17.8655C680.671 -11.4218 766.475 53.5656 793.946 152.421C821.525 251.223 785.193 349.033 698.386 409.234C690.828 414.321 682.467 418.328 675.938 421.966Z"
            stroke="#FF3901" fill="#FF3901" stroke-miterlimit="10" />
    </svg>
</div>

@php
    $locale = app()->getLocale();
    $currentPath = request()->path();
    $strippedPath = Str::after($currentPath, $locale . '/');
@endphp

<div id="menu" class="menucontainer">
    <div id="menuitem1" class="menuitem">
        <a href="/{{ $locale }}/projekte">{{ __('navigation.projekte') }}</a>
    </div>

    <div id="menuitem2" class="menuitem">
        <a href="/{{ $locale }}/über-uns">{{ __('navigation.ueber_uns') }}</a>
    </div>

    <div id="menuitem3" class="menuitem">
        <a href="/{{ $locale }}/über-uns?sinfo=2">{{ __('navigation.kontakt') }}</a>
    </div>

    {{-- Language switch --}}
    <div id="menuitem4" class="menuitem {{ $locale === 'de' ? 'activelink' : '' }}">
        <a href="/de/{{ $strippedPath }}">{{ __('DE') }}</a>
    </div>
    <div id="menuitem5" class="menuitem {{ $locale === 'en' ? 'activelink' : '' }}">
        <a href="/en/{{ $strippedPath }}">{{ __('EN') }}</a>
    </div>
    <div id="menuitemclose" class="menuitem">
            <img id="close_img" src="{{ asset('img/nav/close.svg') }}" alt="Peira Close Menu">
    </div>

    <div id="menuitem6" class="menuitem">
        <a href="/{{ $locale }}">
            <img id="logo" src="{{ asset('img/peira-w.svg') }}" alt="logo" class="logo">
        </a>
    </div>
</div>

@once
    @push('scripts')
        @vite('resources/js/app.js')
    @endpush
@endonce