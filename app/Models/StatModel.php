<?php

namespace App\Models;

use App\Core\DB;

class StatModel
{
    public static function getPublished(): array
    {
        return DB::fetchAll("SELECT * FROM stats WHERE is_published = 1 AND deleted_at IS NULL ORDER BY sort_order ASC");
    }
}
