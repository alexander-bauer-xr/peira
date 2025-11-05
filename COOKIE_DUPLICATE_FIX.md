# Cookie Duplicate Issue - Fix Applied

## Problem Identified

The debug page showed **TWO** `laravel_cookie_consent` cookies:
1. `{"necessary":true,"external":false}` (unencoded, old)
2. `%7B%22necessary%22%3Atrue%2C%22external%22%3Atrue%...` (URL-encoded, new)

This caused the browser to use the old cookie value instead of the updated one.

## Root Cause

Multiple sources were setting cookies:
- Spatie's JavaScript consent dialog
- Our PHP preferences controller
- Potentially different domain/path combinations creating duplicates

## Fix Applied

### 1. Cookie Deletion Before Setting (CookiePreferencesController.php)
```php
// First, queue cookie deletion to clear any duplicates
Cookie::queue(Cookie::forget($cookieName, '/', $cookieDomain));

// Then queue the new cookie
Cookie::queue($cookieName, json_encode($consent), ...);
```

### 2. JavaScript Cookie Cleanup (index.blade.php)
Added `deleteCookie()` function that:
- Deletes cookie for current domain
- Deletes cookie with dot-prefix domain
- Ensures no duplicates exist before setting new cookie
- URL-encodes the cookie value properly

### 3. Form Submission Cleanup (cookie-preferences.blade.php)
Added script to clear all cookie variations before form submission:
```javascript
document.getElementById('cookie-preferences-form').addEventListener('submit', function(e) {
    // Delete all possible cookie variations
    document.cookie = `laravel_cookie_consent=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
    // ... also with domain variations
});
```

## How to Test

### Step 1: Clear All Existing Cookies
1. Open DevTools (F12) → Application tab → Cookies
2. Find all `laravel_cookie_consent` entries
3. Right-click each one → Delete
4. Close and reopen the browser (or use incognito)

### Step 2: Test Initial Consent Dialog
1. Visit `https://peira.sisterqueens.de`
2. Cookie consent popup should appear
3. Check/uncheck "Externe Medien"
4. Click "Einstellungen speichern"
5. Check DevTools → Cookies
6. **Expected**: Only ONE `laravel_cookie_consent` cookie with JSON value

### Step 3: Test Preferences Page
1. Visit `https://peira.sisterqueens.de/de/cookie-einstellungen`
2. Check DevTools → Application → Cookies BEFORE submission
3. Toggle "Externe Medien" checkbox
4. Submit form
5. Check DevTools → Application → Cookies AFTER submission
6. **Expected**: Only ONE cookie, value updated correctly

### Step 4: Verify via Debug Page
1. Visit `https://peira.sisterqueens.de/cookie-debug.php`
2. **Expected results:**
   - Server-Side Cookies: Shows only ONE `laravel_cookie_consent`
   - JavaScript Cookies: Shows only ONE `laravel_cookie_consent`
   - Values match between server and client

### Step 5: Test Persistence
1. Submit preferences with "Externe Medien" checked
2. Reload the page
3. Visit debug page again
4. **Expected**: Cookie still shows `"external":true`

## What Changed

| File | Change | Purpose |
|------|--------|---------|
| `CookiePreferencesController.php` | Added `Cookie::forget()` before `Cookie::queue()` | Clear duplicates server-side |
| `resources/views/vendor/cookie-consent/index.blade.php` | Added `deleteCookie()` function with URL encoding | Clear duplicates client-side |
| `resources/views/cookie-preferences.blade.php` | Added pre-submit cookie cleanup script | Ensure clean state before form POST |

## Expected Behavior After Fix

1. ✅ Only ONE `laravel_cookie_consent` cookie exists at any time
2. ✅ Cookie value is properly URL-encoded JSON
3. ✅ Server and JavaScript see the same cookie value
4. ✅ Updating preferences immediately reflects in the cookie
5. ✅ Cookie persists across page reloads
6. ✅ External media appears/disappears based on consent

## If It Still Doesn't Work

### Check Browser Extensions
Some extensions (Privacy Badger, uBlock Origin, etc.) block cookies:
1. Test in incognito mode with all extensions disabled
2. Check browser console for cookie-related errors

### Check Cookie Attributes
In DevTools → Application → Cookies, verify:
- Domain: `peira.sisterqueens.de` (NOT `.peira.sisterqueens.de`)
- Path: `/`
- Secure: ✓ (checked)
- SameSite: `Lax`
- HttpOnly: (empty/unchecked)

### Force Clear All Cookies
```javascript
// Run in browser console
document.cookie.split(';').forEach(c => {
    document.cookie = c.trim().split('=')[0] + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/';
});
location.reload();
```

## Deploy to Server

After deploying the updated code:

```bash
# On server
git pull origin main
php artisan view:clear
php artisan cache:clear
php artisan config:cache
```

Then clear browser cookies and test!
