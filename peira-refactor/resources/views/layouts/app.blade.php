<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <x-meta :title="$meta->localizedTitle($locale)" :description="$meta->localizedDescription($locale)" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        body {
            font-family: sans-serif;
            padding: 2rem;
            max-width: 800px;
            margin: auto;
            line-height: 1.6;
        }

        article {
            border-bottom: 1px solid #ccc;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }

        time {
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body>
    {{-- Optional shared nav --}}
    <nav>
        <a href="{{ route('home', ['locale' => 'de']) }}">Deutsch</a> |
        <a href="{{ route('home', ['locale' => 'en']) }}">English</a>
    </nav>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>