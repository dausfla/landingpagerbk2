<?php

namespace App\Models;

use App\Core\DB;

class AdvantageModel
{
    public static function getPublished(): array
    {
        return DB::fetchAll("SELECT * FROM advantages WHERE is_published = 1 AND deleted_at IS NULL ORDER BY sort_order ASC");
    }
}
