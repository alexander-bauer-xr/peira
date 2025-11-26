<?php

namespace Tests\Unit;

use App\Services\DrupalApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DrupalApiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_it_prefixes_relative_image_style_urls_with_drupal_base()
    {
        config([
            'services.drupal.base_url' => 'https://drupal.example',
            'services.drupal.username' => 'apiuser',
            'services.drupal.password' => 'secret',
            'services.drupal.consumer_id' => 'test-consumer-uuid',
        ]);

        Http::fake([
            'https://drupal.example/session/token' => Http::response('csrf-token'),
            'https://drupal.example/api/json/file/file/*' => Http::response([
                'data' => [
                    'attributes' => [
                        'image_style_uri' => [
                            'peira_mobile_sm_jpeg' => '/sites/default/files/styles/mobile_sm/public/foo.jpg?itok=abc',
                            'peira_desktop_lg_webp' => 'https://cdn.example/foo.webp',
                        ],
                    ],
                ],
            ]),
        ]);

        $service = new DrupalApiService();

        $file = $service->getFileByUuid('abc-123');
        $styles = $file['data']['attributes']['image_style_uri'];

        $this->assertSame(
            'https://drupal.example/sites/default/files/styles/mobile_sm/public/foo.jpg?itok=abc',
            $styles['peira_mobile_sm_jpeg']
        );
        $this->assertSame('https://cdn.example/foo.webp', $styles['peira_desktop_lg_webp']);
    }
}
