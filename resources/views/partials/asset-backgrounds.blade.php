@php
    $bgSub = asset('img/bg_sub.jpg');
    $bgProj = asset('img/bgproj.jpg');
    $navPageEnd = asset('img/nav/pageend.svg');
    $quoteIcon = asset('img/quote.svg');
@endphp
<style data-asset-backgrounds>
    :root {
        --bg-sub-image: url("{{ $bgSub }}");
        --bg-proj-image: url("{{ $bgProj }}");
        --nav-pageend-image: url("{{ $navPageEnd }}");
        --quote-image: url("{{ $quoteIcon }}");
    }
</style>
