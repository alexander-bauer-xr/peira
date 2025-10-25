<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scrollbarstyle displayblock">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <x-meta :title="$meta->localizedTitle($locale)" :description="$meta->localizedDescription($locale)" />
    @vite(['resources/css/app.css', 'resources/scss/app.scss', 'resources/js/app.js'])
    @include('partials.asset-backgrounds')
    @stack('styles')
</head>

<body>
    @include('partials.menu')

    <main>
        @yield('content')
    </main>

    @include('partials.newsletter-form')
    @stack('scripts')
</body>

</html>