@props(['class' => '', 'styles' => [], 'alt' => ''])

@php
    $map = [
        '(min-width:1440px)' => ['peira_desktop_xl_webp', 'peira_desktop_xl_jpeg'],
        '(min-width:1280px)' => ['peira_desktop_lg_webp', 'peira_desktop_lg_jpeg'],
        '(min-width:1024px)' => ['peira_desktop_md_webp', 'peira_desktop_md_jpeg'],
        '(min-width:  768px)' => ['peira_desktop_sm_webp', 'peira_desktop_sm_jpeg'],
        '(min-width:  640px)' => ['peira_tablet_md_webp',  'peira_tablet_md_jpeg'],
        '(min-width:  576px)' => ['peira_tablet_sm_webp',  'peira_tablet_sm_jpeg'],
        '(min-width:  480px)' => ['peira_mobile_lg_webp',  'peira_mobile_lg_jpeg'],
        '(min-width:  360px)' => ['peira_mobile_md_webp',  'peira_mobile_md_jpeg'],
        'default'            => ['peira_mobile_sm_webp',  'peira_mobile_sm_jpeg'],
    ];
@endphp

<picture>
    @foreach ($map as $mq => $keys)
        @if ($mq !== 'default')
            <source type="image/webp" media="{{ $mq }}" srcset="{{ $styles[$keys[0]] ?? '' }}">
            <source type="image/jpeg" media="{{ $mq }}" srcset="{{ $styles[$keys[1]] ?? '' }}">
        @endif
    @endforeach
    <source type="image/webp" srcset="{{ $styles[$map['default'][0]] ?? '' }}">
    <source type="image/jpeg" srcset="{{ $styles[$map['default'][1]] ?? '' }}">
    <img class="{{ $class }}" alt="{{ $alt }}" src="{{ $styles[$map['default'][1]] ?? '' }}" class="imagecontain">
</picture>
