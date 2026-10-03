<?php

namespace App\Services;

use App\Models\PackageModel;

class PricingService
{
    /**
     * Replace dynamic pricing placeholders in text
     */
    public static function replacePlaceholders(string $text): string
    {
        if (!str_contains($text, '{')) {
            return $text;
        }

        $desainMin = PackageModel::getMinPrice('desain');
        $bangunMin = PackageModel::getMinPrice('bangun');

        $desainBasic = format_rupiah(60000);
        $desainStandard = format_rupiah(80000);
        $desainPremium = format_rupiah(150000);

        $bangunBasic = "Rp4.000.000–4.500.000";
        $bangunStandard = "Rp4.500.000–5.000.000";
        $bangunPremium = "Rp6.000.000–7.500.000";

        $contohDesain120 = format_rupiah(120 * 60000);

        $replacements = [
            '{harga_desain_min}'     => format_rupiah($desainMin),
            '{{harga_desain_min}}'   => format_rupiah($desainMin),
            '{harga_bangun_min}'     => format_rupiah($bangunMin),
            '{{harga_bangun_min}}'   => format_rupiah($bangunMin),
            '{{harga_desain_basic}}' => $desainBasic,
            '{{harga_desain_standard}}' => $desainStandard,
            '{{harga_desain_premium}}'  => $desainPremium,
            '{{harga_bangun_basic}}' => $bangunBasic,
            '{{harga_bangun_standard}}' => $bangunStandard,
            '{{harga_bangun_premium}}'  => $bangunPremium,
            '{{contoh_desain_120}}'  => $contohDesain120,
        ];

        return strtr($text, $replacements);
    }

    /**
     * Format title text: replaces placeholders and converts *highlighted text* to orange spans
     */
    public static function formatTitle(string $text): string
    {
        $text = self::replacePlaceholders($text);
        if (str_contains($text, '*')) {
            $text = preg_replace('/\*(.*?)\*/', '<span style="color: var(--color-orange);">$1</span>', $text);
        }
        return $text;
    }

    /**
     * Format raw database packages array into calculator pricing JSON data
     */
    public static function getPricingJsonData(): array
    {
        $packages = PackageModel::getPublishedPackages();
        $pricingData = [
            'desain' => [],
            'bangun' => []
        ];

        foreach ($packages as $pkg) {
            $type = $pkg['service_type'];
            $pricingData[$type][$pkg['name']] = [
                'id'        => $pkg['id'],
                'name'      => $pkg['name'],
                'price_min' => (int)$pkg['price_min'],
                'price_max' => $pkg['price_max'] !== null ? (int)$pkg['price_max'] : (int)$pkg['price_min'],
                'unit'      => $pkg['unit'],
            ];
        }

        return $pricingData;
    }
}
