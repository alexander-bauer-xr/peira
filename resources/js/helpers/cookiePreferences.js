/**
 * Cookie Preferences Handler
 * Manages cookie consent and reloads external content when preferences change
 */

export function initCookiePreferences() {
    // Handle clicks on "change cookie settings" links
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('change-cookie-consent-link')) {
            e.preventDefault();
            const locale = document.documentElement.lang || 'de';
            window.location.href = `/${locale}/cookie-einstellungen`;
        }
    });

    // Monitor cookie changes and reload placeholders
    watchCookieChanges();
}

/**
 * Watch for cookie changes and reload external content when "external" consent is granted
 */
function watchCookieChanges() {
    const cookieName = 'laravel_cookie_consent';
    let lastCookieValue = getCookie(cookieName);

    // Check every second for cookie changes
    setInterval(() => {
        const currentCookieValue = getCookie(cookieName);
        
        if (currentCookieValue !== lastCookieValue) {
            lastCookieValue = currentCookieValue;
            
            try {
                const consent = JSON.parse(currentCookieValue || '{}');
                
                // If external media consent was granted, reload the page to show videos
                if (consent.external === true) {
                    console.log('External media consent granted, reloading content...');
                    reloadExternalContent();
                }
            } catch (e) {
                console.error('Error parsing cookie consent:', e);
            }
        }
    }, 1000);
}

/**
 * Reload external content (YouTube videos, etc.) after consent is granted
 */
function reloadExternalContent() {
    const placeholders = document.querySelectorAll('.cookie-placeholder');
    
    if (placeholders.length > 0) {
        // Reload the page to fetch content with proper consent
        window.location.reload();
    }
}

/**
 * Get cookie value by name
 */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    
    if (parts.length === 2) {
        return decodeURIComponent(parts.pop().split(';').shift());
    }
    
    return null;
}

/**
 * Check if a specific category is consented
 */
export function hasCookieConsent(category) {
    const cookieName = 'laravel_cookie_consent';
    const cookieValue = getCookie(cookieName);
    
    if (!cookieValue) {
        return false;
    }
    
    try {
        const consent = JSON.parse(cookieValue);
        return consent[category] === true;
    } catch (e) {
        console.error('Error parsing cookie consent:', e);
        return false;
    }
}
