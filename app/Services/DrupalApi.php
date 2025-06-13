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

    public static function getSocialMedia(array $item, string $field = 'field_social_media'): array
    {
        $entries = $item[$field] ?? [];
        $out = [];
        foreach ($entries as $e) {
            $out[] = [
                'first'  => $e['first']  ?? '',
                'second' => $e['second'] ?? '',
                'third'  => $e['third']  ?? null,
            ];
        }
        return $out;
    }
}