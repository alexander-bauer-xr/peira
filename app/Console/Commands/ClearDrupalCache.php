<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDrupalCache extends Command
{
    protected $signature = 'drupal:clear-cache {--images : Clear only image caches} {--all : Clear all Drupal caches}';
    protected $description = 'Clear Drupal API response caches';

    public function handle(): int
    {
        $imagesOnly = $this->option('images');
        $all = $this->option('all');

        if ($imagesOnly) {
            $this->clearImageCaches();
        } elseif ($all) {
            $this->clearAllDrupalCaches();
        } else {
            $this->info('What would you like to clear?');
            $choice = $this->choice(
                'Select cache type',
                ['Image caches only', 'All Drupal caches', 'Cancel'],
                0
            );

            match ($choice) {
                'Image caches only' => $this->clearImageCaches(),
                'All Drupal caches' => $this->clearAllDrupalCaches(),
                default => $this->info('Cancelled'),
            };
        }

        return 0;
    }

    protected function clearImageCaches(): void
    {
        $this->info('Clearing image file caches...');
        
        $keys = Cache::getStore()->getRedis()->keys('*json/file/file/*');
        
        if (empty($keys)) {
            $this->warn('No image caches found. Trying fallback method...');
            // Fallback: Try to clear common patterns
            Cache::flush(); // This clears all caches - use with caution
            $this->info('All caches cleared (fallback method).');
            return;
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        $count = count($keys);
        $this->info("Cleared {$count} image cache entries.");
    }

    protected function clearAllDrupalCaches(): void
    {
        $this->info('Clearing all Drupal API caches...');
        
        $patterns = [
            'api.*',
            'drupal.csrf.token',
        ];

        foreach ($patterns as $pattern) {
            try {
                $keys = Cache::getStore()->getRedis()->keys($pattern);
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
            } catch (\Exception $e) {
                // If Redis keys() doesn't work, use flush
                $this->warn('Could not use pattern matching, clearing all caches...');
                Cache::flush();
                $this->info('All caches cleared.');
                return;
            }
        }

        $this->info('Drupal API caches cleared successfully.');
    }
}
