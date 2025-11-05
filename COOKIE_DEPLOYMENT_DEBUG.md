# Cookie Consent Deployment Debugging Guide

## Problem
Cookie consent works locally but fails on the deployed server.

## Root Causes (Common)
1. **HTTPS detection fails** - Server is behind a proxy/load-balancer and Laravel doesn't detect HTTPS correctly
2. **SESSION_SECURE_COOKIE not set** - Cookies require Secure flag on HTTPS
3. **Wrong APP_URL** - Not using https:// in production
4. **Cached config** - Old configuration cached on server
5. **Domain mismatch** - Cookie domain doesn't match the actual host

---

## Fix Applied

### 1. TrustProxies Middleware (CRITICAL)
Created `app/Http/Middleware/TrustProxies.php` and registered in `bootstrap/app.php`.

**Why**: Most hosting providers (Cloudflare, AWS ELB, NGINX reverse proxy) terminate SSL at the proxy level. Without TrustProxies, Laravel sees HTTP instead of HTTPS, causing `$request->secure()` to return false, which breaks cookie security attributes.

### 2. Session Config Fallback
Updated `CookiePreferencesController.php` to use session config values:
- `config('session.secure')` for Secure flag
- `config('session.domain')` for Domain
- `config('session.same_site')` for SameSite

---

## Deployment Checklist

### On Your Server

#### 1. Update `.env` File
```bash
# Ensure HTTPS URL
APP_URL=https://yourdomain.com

# Force secure cookies on HTTPS
SESSION_SECURE_COOKIE=true

# Optional: Set session domain if needed
# SESSION_DOMAIN=.yourdomain.com
```

#### 2. Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
```

#### 3. Verify PHP Version & Extensions
```bash
php -v  # Should be 8.1+
php -m | grep -E 'json|session|openssl'
```

#### 4. Check File Permissions
```bash
# Laravel needs to write session files
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions
```

---

## Debugging Steps

### Step 1: Inspect Server Response Headers

Run this on your local machine (replace URL):

```bash
curl -i -k https://yourdomain.com/de/cookie-einstellungen \
  -X POST \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "cookie_consent_external=1" \
  -d "cookie_consent_statistics=1" \
  -d "_token=test"
```

**Look for in response:**
```
Set-Cookie: laravel_cookie_consent=...; Path=/; Secure; HttpOnly; SameSite=lax
```

**Red flags:**
- No `Set-Cookie` header = Controller not queuing cookie
- Missing `Secure` flag but site is HTTPS = SESSION_SECURE_COOKIE not set
- Wrong `Domain` = SESSION_DOMAIN misconfigured
- `SameSite=None` without `Secure` = Browser will reject

### Step 2: Browser DevTools Check

1. Open DevTools (F12)
2. Go to **Application** tab → **Cookies**
3. Submit cookie preferences form
4. Check if `laravel_cookie_consent` appears
5. Verify attributes:
   - ✅ Domain matches your site
   - ✅ Path is `/`
   - ✅ Secure is checked (if HTTPS)
   - ✅ SameSite is `lax` or `strict`

### Step 3: Network Tab Inspection

1. Open DevTools → **Network** tab
2. Submit cookie preferences
3. Click the POST request to `/de/cookie-einstellungen`
4. Check **Response Headers** for `Set-Cookie`
5. If missing, server-side issue (check logs)

### Step 4: Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Submit the form and watch for errors.

### Step 5: Verify Proxy Headers

SSH to your server and check if proxy forwards HTTPS info:

```bash
# Check NGINX config (if using NGINX)
cat /etc/nginx/sites-available/your-site

# Should have:
# proxy_set_header X-Forwarded-Proto $scheme;
# proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
```

---

## Common Hosting Provider Specifics

### Cloudflare
- Ensure SSL mode is "Full" or "Full (Strict)" not "Flexible"
- TrustProxies will handle X-Forwarded-Proto header

### AWS/ELB
- ELB terminates SSL, sends X-Forwarded-Proto
- TrustProxies middleware handles this automatically

### Shared Hosting (cPanel, Plesk)
- May not have control over proxy config
- Ensure `.htaccess` doesn't strip headers
- Set `SESSION_SECURE_COOKIE=true` explicitly

### DigitalOcean/Linode
- If using NGINX, check `/etc/nginx/sites-available/default`
- Ensure proxy_set_header directives are present

---

## Testing Checklist

- [ ] `.env` has `APP_URL=https://...`
- [ ] `.env` has `SESSION_SECURE_COOKIE=true`
- [ ] Ran `php artisan config:cache` after .env changes
- [ ] Cleared browser cookies before testing
- [ ] TrustProxies middleware is active (check `bootstrap/app.php`)
- [ ] Server response includes `Set-Cookie` header with `Secure` flag
- [ ] Browser DevTools shows cookie stored with correct attributes
- [ ] Cookie persists across page reloads
- [ ] Cookie preferences page shows saved values on reload

---

## Still Not Working?

### Enable Debug Logging

Add to `CookiePreferencesController.php` update method (temporarily):

```php
\Log::info('Cookie Update Debug', [
    'secure_config' => config('session.secure'),
    'request_secure' => $request->secure(),
    'secure_flag' => $secureFlag,
    'cookie_domain' => $cookieDomain,
    'same_site' => $sameSite,
    'consent' => $consent,
]);
```

Submit the form and check `storage/logs/laravel.log`.

### Check Session Driver

In `.env`:
```bash
SESSION_DRIVER=database  # or file, redis, etc.
```

If using database, ensure `sessions` table exists:
```bash
php artisan migrate
```

### Verify Cookie Encryption

Laravel encrypts cookies by default. Check `app/Http/Middleware/EncryptCookies.php`:

```php
protected $except = [
    'laravel_cookie_consent',  // Must be in except list (Spatie package handles this)
];
```

---

## Quick Test Command

Paste this into your server SSH to test the full flow:

```bash
cd /path/to/your/laravel/app

# Check .env
grep -E 'APP_URL|SESSION_SECURE_COOKIE|SESSION_DOMAIN' .env

# Clear caches
php artisan config:clear && php artisan cache:clear && php artisan config:cache

# Test cookie setting
curl -i -k https://$(grep APP_URL .env | cut -d= -f2 | sed 's|https://||')/de/cookie-einstellungen \
  -X POST \
  -d "cookie_consent_external=1" \
  2>&1 | grep -i "set-cookie"
```

If you see `Set-Cookie: laravel_cookie_consent=...` with `Secure` flag, the server is working correctly. The issue is browser-side.

---

## Summary

**What we fixed:**
1. ✅ Added TrustProxies middleware to detect HTTPS behind proxies
2. ✅ Controller uses session config for secure/domain/sameSite
3. ✅ Provided deployment checklist and debugging commands

**What you need to do on server:**
1. Set `SESSION_SECURE_COOKIE=true` in `.env`
2. Set `APP_URL=https://yourdomain.com` in `.env`
3. Run `php artisan config:cache`
4. Clear browser cookies and test

**If still failing:**
- Check server response headers (curl command above)
- Check browser DevTools → Application → Cookies
- Enable debug logging and check Laravel logs
- Verify proxy configuration (NGINX/Apache)
