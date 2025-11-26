<?php

namespace App\Console\Commands;

use App\Services\DrupalApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DebugDrupalImage extends Command
{
    protected $signature = 'drupal:debug-image {uuid?}';
    protected $description = 'Debug Drupal image style URLs and itok parameters';

    public function handle(DrupalApiService $api): int
    {
        $uuid = $this->argument('uuid');
        
        if (!$uuid) {
            $this->error('Please provide a file UUID to debug');
            $this->info('Usage: php artisan drupal:debug-image <uuid>');
            return 1;
        }

        $this->info("Fetching file data for UUID: {$uuid}");
        $this->info("Drupal URL: " . config('services.drupal.base_url'));
        $consumerId = config('services.drupal.consumer_id');
        if ($consumerId) {
            $this->info("Consumer ID: {$consumerId}");
        } else {
            $this->warn("Consumer ID: NOT CONFIGURED - Set DRUPAL_CONSUMER_ID in .env for correct itok tokens");
        }
        $this->newLine();

        try {
            // Clear cache for this specific file to get fresh data
            Cache::forget("api.json/file/file/{$uuid}." . app()->getLocale());
            
            $file = $api->getFileByUuid($uuid);
            
            $this->info('=== File Data ===');
            $this->line(json_encode($file, JSON_PRETTY_PRINT));
            $this->newLine();

            $styles = data_get($file, 'data.attributes.image_style_uri', []);
            
            if (empty($styles)) {
                $this->warn('No image_style_uri found in the response!');
                $this->info('Available attributes:');
                $this->line(json_encode(array_keys(data_get($file, 'data.attributes', [])), JSON_PRETTY_PRINT));
                return 1;
            }

            $this->info('=== Image Style URLs ===');
            foreach ($styles as $styleName => $url) {
                $this->line("<fg=cyan>{$styleName}</>:");
                $this->line("  {$url}");
                
                // Check if itok is present
                if (preg_match('/[?&]itok=([^&]+)/', $url, $matches)) {
                    $this->line("  <fg=green>✓ itok found: {$matches[1]}</>");
                } else {
                    $this->line("  <fg=red>✗ No itok parameter!</>");
                }
                
                $this->newLine();
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
