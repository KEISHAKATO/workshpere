<?php

namespace App\Services;

class LocationNormalizer
{
    /**
     * Normalize county names:
     * - lowercases & trims
     * - strips common suffix "county"
     * - fixes a few common typos you can expand later
     */
    public static function county(?string $name): ?string
    {
        if (!$name) return null;

        $n = mb_strtolower(trim($name));

        // strip 'county' if present
        $n = preg_replace('/\s*county$/', '', $n);

        
        $fix = [
            'nairobi cnty' => 'nairobi',
            'nairob'       => 'nairobi',
            'mombassa'     => 'mombasa',
            'nbo'          => 'nairobi',
        ];
        if (isset($fix[$n])) $n = $fix[$n];

        return $n ?: null;
    }
}
