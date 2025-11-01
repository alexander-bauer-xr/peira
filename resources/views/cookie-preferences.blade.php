@extends('layouts.app')

@section('content')
    <div id="subpage" class="sub_page scrollbarstyle displayblock" data-page="cookie-preferences">

        @include('partials.social-icons-sub')

        <div class="inner_container_vor">
            <div class="before-margin">
                <h1 class="ueberschrift h1-text">Cookie-Einstellungen</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success cookie-success-message">
                    {{ session('success') }}
                </div>
            @endif

            <div class="wrapper-vor">
                <div class="vorangestellt vor-text">
                    <p>Hier können Sie Ihre Cookie-Einstellungen anpassen. Änderungen werden sofort nach dem Speichern wirksam, und externe Inhalte wie YouTube-Videos werden automatisch geladen.</p>
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
                                            <p>Diese Cookies sind für die Grundfunktionen der Website erforderlich und können nicht deaktiviert werden.</p>
                                        @elseif($key === 'external')
                                            <p>Diese Cookies ermöglichen die Einbettung von externen Medien wie YouTube-Videos, Vimeo-Videos und anderen eingebetteten Inhalten von Drittanbietern.</p>
                                        @elseif($key === 'statistics')
                                            <p>Diese Cookies helfen uns zu verstehen, wie Besucher mit der Website interagieren, indem Informationen anonym gesammelt und gemeldet werden.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="cookie-actions">
                            <button type="submit" class="cookie-consent-button accept-all">
                                Einstellungen speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @include('partials.footer')
    </div>

@push('scripts')
<script>
    // Auto-reload page after successful save to apply changes
    @if(session('success'))
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    @endif
</script>
@endpush

@endsection
