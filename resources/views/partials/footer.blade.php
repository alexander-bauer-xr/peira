<div class="copyright ">
    <div class="impressum">
        <a href="{{ route('cookie.preferences', ['locale' => app()->getLocale()]) }}" class="cookie-settings-link">
            {{ __('content.cookie_settings') }}
        </a>
    </div>
    <div class="copyrighttext">© {{ now()->year }} Peira GbR</div>
</div>