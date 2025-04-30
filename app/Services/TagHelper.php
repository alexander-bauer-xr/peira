<?php
// app/Services/TagHelper.php
namespace App\Services;

class TagHelper
{
    public static function labelById(array $allTags, int|string $tid, string $locale = 'de'): ?string
    {
        foreach ($allTags as $tag) {
            $currentTid = $tag['tid'][0]['value'] ?? null;
            if ((string) $currentTid === (string) $tid) {
                return $locale === 'en'
                    ? ($tag['field_eng'][0]['value'] ?? ($tag['name'][0]['value'] ?? null))
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