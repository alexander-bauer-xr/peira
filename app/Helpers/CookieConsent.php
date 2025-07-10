<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cookie;

class CookieConsent
{
    protected static function data(): array
    {
        return json_decode(
            Cookie::get(config('cookie-consent.cookie_name'), '{}'),
            true
        ) ?: [];
    }

    public static function has(string $category): bool
    {
        return static::data()[$category] ?? false;
    }
}
