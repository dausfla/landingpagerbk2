<?php

namespace App\Controllers\Public;

use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class PageController
{
    public function kebijakanPrivasi(Request $request): void
    {
        Response::html(View::render('public/kebijakan-privasi'));
    }

    public function sitemap(Request $request): void
    {
        $baseUrl = env('APP_URL', 'http://localhost:8000');
        $portfolios = DB::fetchAll("SELECT slug, updated_at FROM portfolios WHERE is_published = 1 AND deleted_at IS NULL");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $baseUrl . '/</loc>' . "\n";
        $xml .= '    <changefreq>daily</changefreq>' . "\n";
        $xml .= '    <priority>1.0</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        // Privacy Policy
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $baseUrl . '/kebijakan-privasi</loc>' . "\n";
        $xml .= '    <changefreq>monthly</changefreq>' . "\n";
        $xml .= '    <priority>0.3</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        // Portfolios
        foreach ($portfolios as $p) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/#portofolio</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d', strtotime($p['updated_at'])) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.7</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        header('Content-Type: application/xml; charset=utf-8');
        echo $xml;
        exit;
    }
}
