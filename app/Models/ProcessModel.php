<?php

namespace App\Models;

use App\Core\DB;

class ProcessModel
{
    public static function getPublished(): array
    {
        return DB::fetchAll("SELECT * FROM process_steps WHERE is_published = 1 AND deleted_at IS NULL ORDER BY step_no ASC");
    }
}
