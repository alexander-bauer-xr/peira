<?php
// app/Http/Controllers/ImageController.php

namespace App\Http\Controllers;

use App\Services\DrupalApiService;

class ImageController extends Controller
{
    public function __invoke(string $locale, string $uuid, DrupalApiService $api)
    {
        $file = $api->getFileByUuid($uuid);
        $attr = data_get($file, 'data.attributes', []);
        $styles = $attr['image_style_uri'] ?? [];
        $alt = $attr['filename'] ?? '';

        return view('image', compact('styles', 'alt'));
    }
}

