<?php

namespace App\Data;

use DateTime;

class TermineItem
{
    public function __construct(
        public string $title,
        public string $titleEn,
        public string $formattedDates,
        public CoProducerItem $place,
        public bool $isVisible = true,
    ) {}

    public static function fromDrupal(array $item, ?CoProducerItem $place = null, string $locale = 'de'): self
    {
        $formattedDates = self::formatDates($item);

        // Fallback falls kein CoProducerItem übergeben wurde
        if (!$place) {
            $place = new CoProducerItem(
                id: '',
                name: 'TBA',
                url: null
            );
        }

        return new self(
            title: $item['field_klartitel_date'][0]['value'] ?? '',
            titleEn: $item['field_klartitel_eng'][0]['value'] ?? '',
            formattedDates: $formattedDates,
            place: $place
        );
    }

    private static function formatDates(array $item): string
    {
        $dateEntries = $item['field_datum_und_uhrzeit'] ?? [];
        $withTime = ($item['field_uhrzeit_angeben_'][0]['value'] ?? false) === true;
        $output = '';

        if (count($dateEntries) > 1) {
            $grouped = [];

            foreach ($dateEntries as $entry) {
                $dt = new DateTime($entry['value']);
                $key = $dt->format('.m.y');
                $grouped[$key][] = $dt->format('d');
                $lastTime = $dt->format('H:i');
            }

            foreach ($grouped as $monthYear => $days) {
                $joinedDays = implode('/', $days);
                $output .= $joinedDays . $monthYear;
                if ($withTime) {
                    $output .= ' ‧ ' . $lastTime;
                }
                $output .= PHP_EOL;
            }
        } else {
            $start = isset($dateEntries[0]['value']) ? strtotime($dateEntries[0]['value']) : null;
            $end = isset($item['field_bis_uhrzeit'][0]['value']) ? strtotime($item['field_bis_uhrzeit'][0]['value']) : null;

            if ($start && $end) {
                $output = date('d.m', $start) . ' - ' . date('d.m.y', $end);
            } elseif ($start) {
                $output = date($withTime ? 'd.m.y ‧ H:i' : 'd.m.y', $start);
            }
        }

        return trim($output);
    }

    public function localizedTitle(string $locale): string
    {
        return $locale === 'en' && $this->titleEn ? $this->titleEn : $this->title;
    }
}
