{{-- resources/views/image.blade.php --}}
@extends('layouts.plain')

@section('content')
@php
    // map “min-width → style key prefix”
    $breakpoints = [
        1280 => 'peira_desktop_xl',
        1024 => 'peira_desktop_lg',
        768  => 'peira_desktop_md',
        0    => 'peira_mobile_sm',
    ];
@endphp

<picture>
    @foreach($breakpoints as $min => $prefix)
        @php
            $w = $styles["{$prefix}_webp"] ?? null;
            $j = $styles["{$prefix}_jpeg"] ?? null;
            $m = $min > 0 ? "(min-width:{$min}px)" : null;
        @endphp

        @if($w)
            <source
                type="image/webp"
                {{ $m ? "media=\"$m\"" : '' }}
                srcset="{{ $w }}"
            >
        @endif

        @if($j)
            <source
                type="image/jpeg"
                {{ $m ? "media=\"$m\"" : '' }}
                srcset="{{ $j }}"
            >
        @endif
    @endforeach

    {{-- Fallback for very old browsers --}}
    <img
        src="{{ $styles['peira_mobile_sm_jpeg'] ?? reset($styles) }}"
        alt="{{ $alt }}"
        class="imagecontain"
    >
</picture>
@endsection