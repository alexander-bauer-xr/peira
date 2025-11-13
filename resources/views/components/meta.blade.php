<meta charset="UTF-8">
<title>{{ $title ?? 'Peira' }}</title>
<meta name="description" content="{{ $description ?? 'Künstlerische Plattform & Projekte' }}">

<!-- Open Graph / Facebook -->
<meta property="og:title" content="{{ $title ?? 'Peira' }}">
<meta property="og:description" content="{{ $description ?? '' }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $image ?? asset('/img/header.png') }}">

<!-- SEO -->
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">

<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">

<!-- Theming -->
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">