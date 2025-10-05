<?php
namespace App\Helpers;

use DOMDocument;
use DOMXPath;

use App\Helpers\CookieConsent;
class HtmlTransformHelper
{
    public static function processHtml(?string $htmlContent): string
    {
        if ($htmlContent === null || $htmlContent === '') {
            return '';
        }

        $htmlContent = self::ensureAbsoluteAssetUrls($htmlContent);

        return self::transformContent($htmlContent);
    }

    private static function transformContent(string $htmlContent): string
    {
        $previous = libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $wrappedHtml = self::wrapHtml($htmlContent);

        if (!$doc->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            return $htmlContent;
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($doc);

        $hasExternalConsent = CookieConsent::has('external');

        self::transformVideoElements($doc, $xpath);
        self::transformIframeElements($doc, $xpath, $hasExternalConsent);

        if (!$hasExternalConsent) {
            self::replaceIframesWithPlaceholders($doc, $xpath);
        }

        return self::extractInnerHtml($doc);
    }

    private static function transformVideoElements(DOMDocument $doc, DOMXPath $xpath): void
    {
        $videoElements = $xpath->query('//article//video');
        $counter = 0;

        foreach ($videoElements as $videoElement) {
            if (!$videoElement instanceof \DOMElement) {
                continue;
            }

            $counter++;
            $videoElement->setAttribute('class', 'video-js vjs-default-skin vjs-fluid');
            $videoElement->setAttribute('controls', '');
            $videoElement->setAttribute('preload', 'auto');
            $videoElement->setAttribute('data-setup', '{}');
            $videoElement->setAttribute('id', 'my-video-' . $counter);

            $article = $xpath->query('ancestor::article', $videoElement)->item(0);
            if ($article instanceof \DOMElement) {
                $article->parentNode?->replaceChild($videoElement, $article);
            }
        }
    }

    private static function transformIframeElements(DOMDocument $doc, DOMXPath $xpath, bool $hasExternalConsent): void
    {
        $baseUrl = rtrim(config('services.drupal.base_url', ''), '/');
        $iframes = $xpath->query('//iframe');

        foreach ($iframes as $iframe) {
            if (!$iframe instanceof \DOMElement) {
                continue;
            }

            self::convertAttributeToAbsolute($iframe, 'src', $baseUrl);
            self::convertAttributeToAbsolute($iframe, 'data-src', $baseUrl);

            if ($hasExternalConsent) {
                $iframe->setAttribute('loading', $iframe->getAttribute('loading') ?: 'lazy');
                $iframe->setAttribute('referrerpolicy', $iframe->getAttribute('referrerpolicy') ?: 'strict-origin-when-cross-origin');
            }
        }
    }

    private static function replaceIframesWithPlaceholders(DOMDocument $doc, DOMXPath $xpath): void
    {
        $iframes = $xpath->query('//article[contains(@class, "media--type-remote-video")]//iframe');

        foreach ($iframes as $iframe) {
            if (!$iframe instanceof \DOMElement) {
                continue;
            }

            $article = $xpath->query('ancestor::article', $iframe)->item(0);

            if (!$article instanceof \DOMElement) {
                continue;
            }

            $placeholder = $doc->createElement('div');
            $placeholder->setAttribute('class', 'cookie-placeholder');
            $placeholder->setAttribute('data-src', $iframe->getAttribute('src'));

            $paragraph = $doc->createElement('p', "To see this content, you need to accept 'External Media' cookies.");
            $link = $doc->createElement('a', 'Change cookie settings');
            $link->setAttribute('href', '#');
            $link->setAttribute('class', 'change-cookie-consent-link');

            $placeholder->appendChild($paragraph);
            $placeholder->appendChild($link);

            $article->parentNode?->replaceChild($placeholder, $article);
        }
    }

    private static function ensureAbsoluteAssetUrls(string $htmlContent): string
    {
        $baseUrl = rtrim(config('services.drupal.base_url', ''), '/');

        if ($baseUrl === '') {
            return $htmlContent;
        }

        $previous = libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $wrappedHtml = self::wrapHtml($htmlContent);

        if (!$doc->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            return $htmlContent;
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($doc);

        foreach ($xpath->query('//img') as $img) {
            if (!$img instanceof \DOMElement) {
                continue;
            }
            self::convertAttributeToAbsolute($img, 'src', $baseUrl);
            self::convertAttributeToAbsolute($img, 'data-src', $baseUrl);

            if ($img->hasAttribute('srcset')) {
                $img->setAttribute('srcset', self::normalizeSrcset($img->getAttribute('srcset'), $baseUrl));
            }

            self::sanitizeInlineImageAttributes($img);
        }

        foreach ($xpath->query('//source') as $source) {
            if (!$source instanceof \DOMElement) {
                continue;
            }
            self::convertAttributeToAbsolute($source, 'src', $baseUrl);

            if ($source->hasAttribute('srcset')) {
                $source->setAttribute('srcset', self::normalizeSrcset($source->getAttribute('srcset'), $baseUrl));
            }
        }

        foreach ($xpath->query('//a[@href]') as $anchor) {
            if (!$anchor instanceof \DOMElement) {
                continue;
            }
            $href = $anchor->getAttribute('href');
            if ($href !== '' && self::shouldConvertPath($href)) {
                $anchor->setAttribute('href', self::convertToAbsolute($href, $baseUrl));
            }
        }

        return self::extractInnerHtml($doc);
    }

    private static function convertAttributeToAbsolute(\DOMElement $node, string $attribute, string $baseUrl): void
    {
        if (!$node->hasAttribute($attribute)) {
            return;
        }

        $value = $node->getAttribute($attribute);

        if ($value === '') {
            return;
        }

        if ($attribute === 'srcset') {
            $node->setAttribute('srcset', self::normalizeSrcset($value, $baseUrl));
            return;
        }

        $absolute = self::convertToAbsolute($value, $baseUrl);

        if ($absolute !== $value) {
            $node->setAttribute($attribute, $absolute);
        }
    }

    private static function normalizeSrcset(string $srcset, string $baseUrl): string
    {
        $parts = array_filter(array_map('trim', explode(',', $srcset)), fn($part) => $part !== '');

        $normalized = array_map(function ($part) use ($baseUrl) {
            $segments = preg_split('/\s+/', $part, 2);
            $url = $segments[0] ?? '';
            $descriptor = $segments[1] ?? '';

            $convertedUrl = self::convertToAbsolute($url, $baseUrl);

            return trim($descriptor) !== ''
                ? $convertedUrl . ' ' . $descriptor
                : $convertedUrl;
        }, $parts);

        return implode(', ', $normalized);
    }

    private static function shouldConvertPath(string $value): bool
    {
        $trimmed = trim($value);

        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            return false;
        }

        return !preg_match('#^(?:[a-z][a-z0-9+.-]*:|//)#i', $trimmed);
    }

    private static function convertToAbsolute(string $value, string $baseUrl): string
    {
        $trimmed = trim($value);

        if ($trimmed === '' || preg_match('#^(?:[a-z][a-z0-9+.-]*:|//)#i', $trimmed) || str_starts_with($trimmed, 'data:')) {
            return $trimmed;
        }

        if (str_starts_with($trimmed, '/')) {
            return $baseUrl . $trimmed;
        }

        return $baseUrl . '/' . ltrim($trimmed, '/');
    }

    private static function wrapHtml(string $htmlContent): string
    {
        $convmap = [0x80, 0x10FFFF, 0, 0xFFFF];
        $encoded = mb_encode_numericentity($htmlContent, $convmap, 'UTF-8', true);

        return '<!DOCTYPE html><html><body>' . $encoded . '</body></html>';
    }

    private static function extractInnerHtml(DOMDocument $doc): string
    {
        $body = $doc->getElementsByTagName('body')->item(0);
        $innerHTML = '';

        if (!$body) {
            return $innerHTML;
        }

        foreach ($body->childNodes as $child) {
            $innerHTML .= $doc->saveHTML($child);
        }

        return $innerHTML;
    }

    private static function sanitizeInlineImageAttributes(\DOMElement $img): void
    {
        if ($img->hasAttribute('class')) {
            $classes = preg_split('/\s+/', trim($img->getAttribute('class')));
            $classes = array_filter(
                $classes,
                fn($class) => $class !== '' && strcasecmp($class, 'filter-image-invalid') !== 0
            );

            if (empty($classes)) {
                $img->removeAttribute('class');
            } else {
                $img->setAttribute('class', implode(' ', $classes));
            }
        }

        if ($img->hasAttribute('title') && self::isDrupalRemovedTitle($img->getAttribute('title'))) {
            $img->removeAttribute('title');
        }

        if ($img->hasAttribute('alt') && self::isDrupalRemovedAlt($img->getAttribute('alt'))) {
            $img->setAttribute('alt', '');
        }
    }

    private static function isDrupalRemovedTitle(string $value): bool
    {
        return str_contains($value, 'Bild wurde entfernt')
            || str_contains($value, 'aus Sicherheitsgründen');
    }

    private static function isDrupalRemovedAlt(string $value): bool
    {
        return trim($value) === 'Bild entfernt.'
            || self::isDrupalRemovedTitle($value);
    }
}