<?php

namespace App\Services;

class SocialMediaRenderer
{
    public function render(array $entries): string
    {
        if (empty($entries)) {
            return '';
        }

        $html = '<div class="termine">';
        $html .= '<div class="newshead small-text">' . __('content.social') . '</div>';
        $html .= '<div class="social-media-grid">';

        foreach ($entries as $entry) {
            $label = e($entry['first'] ?? '');
            $handle = e($entry['second'] ?? '');
            $url = $entry['third'] ?? null;

            if (!$label || !$url) {
                continue;
            }

            $html .= '<div class="social-media-row d-flex flex-row gap-2">';
            $html .= '<div class="social-media-first">' . $label . '</div>';
            $html .= '<a href="' . e($url) . '" target="_blank" class="social-media-second">' . $handle . ' </a>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
