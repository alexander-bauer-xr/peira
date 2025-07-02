@if($cookieConsentConfig['enabled'] && ! $alreadyConsentedWithCookies)

    @include('cookie-consent::dialogContents')

    <script>
        window.laravelCookieConsent = (function () {

            const COOKIE_DOMAIN = '{{ config('session.domain') ?? request()->getHost() }}';
            const COOKIE_NAME = '{{ $cookieConsentConfig['cookie_name'] }}';

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                
                // Convert value to a JSON string if it's an object
                const cookieValue = typeof value === 'object' ? JSON.stringify(value) : value;
                
                document.cookie = `${name}=${cookieValue}`
                    + `;expires=${date.toUTCString()}`
                    + `;domain=${COOKIE_DOMAIN}`
                    + `;path=/{{ config('session.secure') ? ';secure' : '' }}`
                    + `{{ config('session.same_site') ? ';samesite='.config('session.same_site') : '' }}`;
            }
            
            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-consent');
                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
            }

            // ✨ New function to save selected preferences
            function savePreferences() {
                const checkboxes = document.querySelectorAll('.js-cookie-consent-category');
                const allowedCategories = {};

                checkboxes.forEach(checkbox => {
                    // Store category name and a boolean indicating if it's checked
                    allowedCategories[checkbox.value] = checkbox.checked;
                });
                
                // The 'necessary' checkbox is disabled but checked, ensure it's always true
                allowedCategories['necessary'] = true;

                setCookie(COOKIE_NAME, allowedCategories, {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }

            // ✨ New function to consent to all categories
            function consentToAll() {
                const allCategories = {!! json_encode(array_fill_keys(array_keys($cookieConsentConfig['categories']), true)) !!};

                setCookie(COOKIE_NAME, allCategories, {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }

            // Attach event listeners to the new buttons
            document.querySelector('.js-cookie-consent-save').addEventListener('click', savePreferences);
            document.querySelector('.js-cookie-consent-agree-all').addEventListener('click', consentToAll);
            
            return {
                hideCookieDialog: hideCookieDialog
            };
        })();
    </script>

@endif