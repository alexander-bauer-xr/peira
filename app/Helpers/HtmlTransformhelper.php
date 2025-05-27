<?php

namespace App\Helpers;

use DOMDocument;
use DOMXPath;

class HtmlTransformHelper
{
    public static function replaceVideo(string $htmlContent): string
    {
        libxml_use_internal_errors(true); // Suppress warnings

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
}