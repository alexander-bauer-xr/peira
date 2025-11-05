@extends('layouts.app')

@section('content')
    <div id="subpage" class="sub_page scrollbarstyle displayblock" data-page="cookie-preferences">

        @include('partials.social-icons-sub')

        <div class="inner_container_vor">
            <div class="before-margin">
                <h1 class="ueberschrift h1-text">{{ __('content.cookie_settings') }}</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success cookie-success-message">
                    {{ __('content.cookie_settings_saved') }}
                </div>
            @endif

            <div class="wrapper-vor">
                <div class="vorangestellt vor-text">
                    <p>{{ __('content.cookie_intro') }}</p>
                </div>
            </div>

            <div class="inner_container">
                <div class="content body-text">
                    <form id="cookie-preferences-form" method="POST" action="{{ route('cookie.update', ['locale' => $locale]) }}">
                        @csrf
                        
                        <div class="cookie-categories-list">
                            @foreach ($categories as $key => $label)
                                <div class="cookie-category-card">
                                    <div class="cookie-category-header">
                                        <input 
                                            type="checkbox" 
                                            id="cookie-pref-{{ $key }}"
                                            name="cookie_consent_{{ $key }}" 
                                            value="1"
                                            class="cookie-checkbox"
                                            {{ $key === 'necessary' ? 'checked disabled' : '' }}
                                            {{ isset($currentConsent[$key]) && $currentConsent[$key] ? 'checked' : '' }}
                                        >
                                        <label for="cookie-pref-{{ $key }}" class="cookie-label">
                                            <strong>{{ $label }}</strong>
                                        </label>
                                    </div>
                                    
                                    <div class="cookie-category-description">
                                        @if($key === 'necessary')
                                            <p>{{ __('content.cookie_necessary_desc') }}</p>
                                        @elseif($key === 'external')
                                            <p>{{ __('content.cookie_external_desc') }}</p>
                                        @elseif($key === 'statistics')
                                            <p>{{ __('content.cookie_statistics_desc') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="cookie-actions">
                            <button type="submit" class="cookie-consent-button ok_cookie_box_style accept-all">
                                {{ __('content.cookie_save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @include('partials.footer')
    </div>

@endsection

@push('scripts')
<script>
    // Clear any duplicate cookies before form submission
    document.getElementById('cookie-preferences-form').addEventListener('submit', function(e) {
        const cookieName = 'laravel_cookie_consent';
        
        // Delete all possible cookie variations
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain={{ request()->getHost() }}`;
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.{{ request()->getHost() }}`;
    });
</script>

@if(session('success'))
<script>
    // Handle successful save
    // Check if we came from a page with content that needs reloading
    const referrer = document.referrer;
    const shouldReload = referrer && !referrer.includes('cookie-einstellungen');
    
    if (shouldReload) {
        // Redirect back to the previous page to show new content
        setTimeout(() => {
            window.location.href = referrer;
        }, 1500);
    }
</script>
@endif
@endpush
