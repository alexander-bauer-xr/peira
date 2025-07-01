@props([
    'href'      => '#',
    'active'    => false,
    'external'  => false,
    'label'     => null,    // optional: für aria-label
])

@php
    // aria-current setzen
    $ariaCurrent = $active ? 'page' : null;
    $rel         = $external ? 'noopener noreferrer' : null;
    $target      = $external ? '_blank' : null;
    // aria-label: entweder explicit oder bei externem Link automatisch ergänzen
    if ($label) {
        $ariaLabel = $label;
    } elseif ($external) {
        // Slot may contain HTML, so use explicit label prop for complex labels
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