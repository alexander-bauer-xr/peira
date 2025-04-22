<?php
// app/Services/TagHelper.php
namespace App\Services;

class TagHelper
{
    public static function labelById(array $allTags, int $tid, string $locale = 'de'): ?string
    {
        foreach ($allTags as $tag) {
            if (($tag['tid'][0]['value'] ?? null) === $tid) {
                return $locale === 'en'
                    ? ($tag['field_eng'][0]['value'] ?? null)
                    : ($tag['name'][0]['value'] ?? null);
            }
        }
        return null;
    }

    public static function labels(array $allTags, array $tids, string $locale = 'de'): array
    {
        return array_filter(array_map(
            fn($tid) => self::labelById($allTags, $tid, $locale),
            $tids
        ));
    }
}