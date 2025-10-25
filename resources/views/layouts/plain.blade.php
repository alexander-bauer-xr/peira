{{-- resources/views/layouts/plain.blade.php --}}
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $alt ?? 'Image' }}</title>
  @vite(['resources/css/app.css', 'resources/scss/app.scss', 'resources/js/app.js'])
  @include('partials.asset-backgrounds')
</head>
<body class="p-6">
  @yield('content')
</body>
</html>