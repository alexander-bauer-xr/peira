<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Data\MetaData;

class CookiePreferencesController extends Controller
{
    public function show(string $locale = 'de')
    {
        app()->setLocale($locale);
        
        $categories = config('cookie-consent.categories');
        $cookieName = config('cookie-consent.cookie_name');
        $currentConsent = json_decode(Cookie::get($cookieName, '{}'), true) ?: [];
        
        $meta = new MetaData(
            title: 'Peira - Cookie-Einstellungen',
            titleEn: 'Peira - Cookie Preferences',
            description: 'Verwalten Sie Ihre Cookie-Einstellungen und Datenschutzpräferenzen.',
            descriptionEn: 'Manage your cookie settings and privacy preferences.'
        );
        
        return view('cookie-preferences', [
            'categories' => $categories,
            'currentConsent' => $currentConsent,
            'locale' => $locale,
            'meta' => $meta,
        ]);
    }

    public function update(Request $request, string $locale = 'de')
    {
        $categories = config('cookie-consent.categories');
        $cookieName = config('cookie-consent.cookie_name');
        $cookieLifetime = config('cookie-consent.cookie_lifetime');
        
        $consent = [];
        
        foreach ($categories as $key => $label) {
            // Necessary cookies are always enabled
            if ($key === 'necessary') {
                $consent[$key] = true;
            } else {
                $consent[$key] = $request->has("cookie_consent_{$key}");
            }
        }
        
        $secureFlag = config('session.secure') ?? $request->secure();
        $cookieDomain = config('session.domain');
        $sameSite = config('session.same_site', 'lax');

        // First, queue cookie deletion to clear any duplicates
        Cookie::queue(Cookie::forget($cookieName, '/', $cookieDomain));
        
        // Then queue the new cookie with updated consent
        Cookie::queue(
            $cookieName,
            json_encode($consent),
            $cookieLifetime,
            '/',
            $cookieDomain,  // domain (null = current domain)
            (bool) $secureFlag,  // secure (true for HTTPS)
            false,  // httpOnly (false so JavaScript can read it)
            false,  // raw
            $sameSite   // sameSite
        );
        
        return redirect()
            ->back()
            ->with('success', __('content.cookie_settings_saved'));
    }
}
