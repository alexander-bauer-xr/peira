@php
    $bgSubLowRes = asset('img/bg_sub.jpg');
    $bgSubHighRes = asset('img/bg_sub.png');
    $bgProjLowRes = asset('img/bgproj.jpg');
    $bgProjHighRes = asset('img/bgproj.png');
    $navPageEnd = asset('img/nav/pageend.svg');
    $quoteIcon = asset('img/quote.svg');
@endphp
<style data-asset-backgrounds>
    :root {
        --bg-sub-image-lowres: url("{{ $bgSubLowRes }}");
        --bg-sub-image: url("{{ $bgSubHighRes }}");
        --bg-proj-image-lowres: url("{{ $bgProjLowRes }}");
        --bg-proj-image: url("{{ $bgProjHighRes }}");
        --nav-pageend-image: url("{{ $navPageEnd }}");
        --quote-image: url("{{ $quoteIcon }}");
    }
</style>
