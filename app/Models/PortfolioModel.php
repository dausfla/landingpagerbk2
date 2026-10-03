<?php

namespace App\Models;

use App\Core\DB;

class PortfolioModel
{
    public static function getCategories(): array
    {
        return DB::fetchAll("SELECT * FROM portfolio_categories ORDER BY sort_order ASC");
    }

    public static function getPublishedPortfolios(): array
    {
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM portfolios p
                LEFT JOIN portfolio_categories c ON p.category_id = c.id
                WHERE p.is_published = 1 AND p.deleted_at IS NULL
                ORDER BY p.sort_order ASC";
        return DB::fetchAll($sql);
    }
}
