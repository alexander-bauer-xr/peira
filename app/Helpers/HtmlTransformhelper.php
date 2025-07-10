<?php
namespace App\Helpers;

use DOMDocument;
use DOMXPath;

use App\Helpers\CookieConsent;
class HtmlTransformHelper
{
    public static function processHtml(string $htmlContent): string
    {
        if (str_contains($htmlContent, '<iframe')) {
            return self::transformIframeWithConsent($htmlContent);
        }

        return self::transformVideoTag($htmlContent);
    }

    private static function transformVideoTag(string $htmlContent): string
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $wrappedHtml = '<!DOCTYPE html><html><body>' . mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8') . '</body></html>';
        $doc->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $videoElements = $xpath->query('//article//video');
        $counter = 0;

        foreach ($videoElements as $videoElement) {
            $counter++;
            $videoElement->setAttribute('class', 'video-js vjs-default-skin vjs-fluid');
            $videoElement->setAttribute('controls', '');
            $videoElement->setAttribute('preload', 'auto');
            $videoElement->setAttribute('data-setup', '{}');
            $videoElement->setAttribute('id', 'my-video-' . $counter);

            $article = $xpath->query('ancestor::article', $videoElement)->item(0);
            if ($article) {
                $article->parentNode->replaceChild($videoElement, $article);
            }
        }

        $body = $doc->getElementsByTagName('body')->item(0);
        $innerHTML = '';
        foreach ($body->childNodes as $child) {
            $innerHTML .= $doc->saveHTML($child);
        }

        return $innerHTML;
    }

    /**
     * Transforms <iframe> tags based on cookie consent.
     * Also made 'private' as it's called by processHtml.
     */
    private static function transformIframeWithConsent(string $htmlContent): string
    {
        if (CookieConsent::has('external')) {
            return $htmlContent;
        }

        // No consent, replace iframe with a placeholder.
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $wrappedHtml = '<!DOCTYPE html><html><body>' . mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8') . '</body></html>';
        $doc->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $iframes = $xpath->query('//article[contains(@class, "media--type-remote-video")]//iframe');

        foreach ($iframes as $iframe) {
            $article = $xpath->query('ancestor::article', $iframe)->item(0);

            if ($article) {
                $placeholder = $doc->createElement('div');
                $placeholder->setAttribute('class', 'cookie-placeholder');
                $placeholder->setAttribute('data-src', $iframe->getAttribute('src'));

                $paragraph = $doc->createElement('p', "To see this content, you need to accept 'External Media' cookies.");
                $link = $doc->createElement('a', 'Change cookie settings');
                $link->setAttribute('href', '#');
                $link->setAttribute('class', 'change-cookie-consent-link');

                $placeholder->appendChild($paragraph);
                $placeholder->appendChild($link);

                $article->parentNode->replaceChild($placeholder, $article);
            }
        }

        $body = $doc->getElementsByTagName('body')->item(0);
        $innerHTML = '';
        foreach ($body->childNodes as $child) {
            $innerHTML .= $doc->saveHTML($child);
        }
        return $innerHTML;
    }
}