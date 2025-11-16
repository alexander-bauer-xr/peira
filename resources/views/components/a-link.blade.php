@props([
    'href'      => '#',
    'active'    => false,
    'external'  => false,
    'label'     => null,  
])

@php
    $ariaCurrent = $active ? 'page' : null;
    $rel         = $external ? 'noopener noreferrer' : null;
    $target      = $external ? '_blank' : null;
    if ($label) {
        $ariaLabel = $label;
    } elseif ($external) {
        $ariaLabel = trim(strip_tags($slot)) . ' (öffnet in neuem Tab)';
    } else {
        $ariaLabel = null;
    }
@endphp

<a
    href="{{ $href }}"
    @if($ariaCurrent) aria-current="{{ $ariaCurrent }}" @endif
    @if($rel) rel="{{ $rel }}" @endif
    @if($target) target="{{ $target }}" @endif
    @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
    {{ $attributes->merge(['class' => 'underline focus:outline-2 focus:outline-offset-2']) }}
>
    {{ $slot }}
</a>