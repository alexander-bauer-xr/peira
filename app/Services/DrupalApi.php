<?php
namespace App\Services;

class DrupalApi {
    public static function get(array $item, string $key, string $subkey = 'value'): ?string {
        return $item[$key][0][$subkey] ?? null;
    }

    public static function getProcessed(array $item, string $key): ?string {
        return $item[$key][0]['processed'] ?? null;
    }

    public static function getArray(array $item, string $key): ?array {
        return $item[$key] ?? [];
    }
}