{{-- Deprecated wrapper to support legacy includes. Use info/page.blade.php going forward. --}}
@include('info.page', [
    'item' => $item ?? null,
    'locale' => $locale ?? app()->getLocale(),
    'meta' => $meta ?? null,
    'subinfos' => $subinfos ?? [],
    'slugs' => $slugs ?? [],
    'activeTab' => $activeTab ?? 0,
    'pageTitle' => $pageTitle ?? (($locale ?? app()->getLocale()) === 'en' ? 'About us' : 'Über uns'),
    'pageKey' => $pageKey ?? 'about',
    'routeSegment' => $routeSegment ?? 'ueber-uns',
])