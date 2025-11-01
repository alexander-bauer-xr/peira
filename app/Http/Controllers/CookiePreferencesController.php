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
        
        $cookie = Cookie::make(
            $cookieName,
            json_encode($consent),
            $cookieLifetime,
            '/',
            null,
            false,
            false
        );
        
        return redirect()
            ->back()
            ->withCookie($cookie)
            ->with('success', 'Cookie-Einstellungen wurden gespeichert.');
    }
}
