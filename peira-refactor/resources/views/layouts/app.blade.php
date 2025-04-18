<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <x-meta :title="$meta->localizedTitle($locale)" :description="$meta->localizedDescription($locale)" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
    @include('partials.menu')
    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>