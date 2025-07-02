<?php
// app/Console/Commands/RefactorLinks.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RefactorLinks extends Command
{
    protected $signature = 'refactor:links {--placeholder : Use placeholder for aria-label}';
    protected $description = 'Replace <a href> in Blade with <x-a-link> component';

    public function handle()
    {
        $usePlaceholder = $this->option('placeholder');
        $viewsPath      = resource_path('views');
        $files = File::allFiles($viewsPath);

        foreach ($files as $file) {
            $path = $file->getRealPath();

            // Skip component files
            if (strpos($path, $viewsPath . DIRECTORY_SEPARATOR . 'components') === 0) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($path);

            $new = preg_replace_callback(
                '/<a\b(?![^>]*x-a-link)[^>]*href="([^"]+)"([^>]*)>([\s\S]*?)<\/a>/i',
                function ($matches) use ($usePlaceholder) {
                    // $matches[1]: URL
                    // $matches[2]: other attrs
                    // $matches[3]: inner HTML/text
                    $url        = $matches[1];
                    $otherAttrs = trim($matches[2]);
                    $innerHtml  = $matches[3];

                    if ($usePlaceholder) {
                        $label = '__REPLACE_LABEL__';
                    } else {
                        // strip tags and decode for aria-label
                        $label = trim(strip_tags($innerHtml));
                        $label = html_entity_decode($label, ENT_QUOTES);
                    }

                    // Build component string
                    $attrsPart = $otherAttrs ? ' ' . $otherAttrs : '';
                    return "<x-a-link href=\"{$url}\"{$attrsPart} label=\"{$label}\">{$innerHtml}</x-a-link>";
                },
                $content
            );

            if ($new !== null && $new !== $content) {
                File::copy($path, "{$path}.bak");
                File::put($path, $new);
                $this->info("Updated: {$path}");
            }
        }
    }
}