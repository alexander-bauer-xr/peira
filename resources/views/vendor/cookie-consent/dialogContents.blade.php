@php
    $categories = config('cookie-consent.categories');
@endphp

{{-- We add your ID and remove Tailwind classes for positioning, color, and shadow --}}
<div id="cookie_consent_popup" class="js-cookie-consent cookie-consent z-50">

    {{-- Container for the text and buttons --}}
    <div class="cookie-consent-content">

        {{-- Main Text Area --}}
        <div class="cookie-consent-message">
            <h2>Cookies</h2>
            <p>{!! trans('cookie-consent::texts.message') !!}</p>
        </div>

        {{-- Checkbox Categories --}}
        <div class="cookie-categories-column">
            <div class="cookie-category-item">{!! trans('cookie-consent::texts.cookie-message') !!}</div>
            <div class="cookie-categories-grid">
                {{-- Loop through each category and create a checkbox --}}
                @foreach ($categories as $key => $label)
                    <div class="cookie-category-item">
                        <input type="checkbox" id="cookie-consent-{{ $key }}"
                            name="cookie_consent_{{ $key }}" value="{{ $key }}"
                            class="js-cookie-consent-category"
                            {{ $key === 'necessary' ? 'checked disabled' : 'checked' }}>
                        <label for="cookie-consent-{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="cookie-actions">
            {{-- We apply a new class to style both buttons consistently --}}
            <button class="js-cookie-consent-save cookie-consent-button">
                {{ trans('cookie-consent::texts.decline') }}
            </button>
            <button class="js-cookie-consent-agree-all cookie-consent-button accept-all">
                {{ trans('cookie-consent::texts.agree') }}
            </button>
        </div>

    </div>
</div>
