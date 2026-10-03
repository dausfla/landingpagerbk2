<?php

namespace App\Models;

use App\Core\DB;
use App\Services\PricingService;

class FaqModel
{
    public static function getPublished(): array
    {
        $faqs = DB::fetchAll("SELECT * FROM faqs WHERE is_published = 1 AND deleted_at IS NULL ORDER BY sort_order ASC");
        foreach ($faqs as &$faq) {
            $faq['answer'] = PricingService::replacePlaceholders($faq['answer']);
        }
        return $faqs;
    }
}
