<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Cache;
use App\Models\AdvantageModel;
use App\Models\ProcessModel;
use App\Models\FaqModel;
use App\Models\ArticleModel;
use App\Models\StatModel;
use App\Models\ActivityLogModel;

class ContentController
{
    public function advantages(Request $request): void
    {
        $advantages = AdvantageModel::getPublished();
        $html = View::renderWithLayout('admin/content/advantages', 'admin/layout', [
            'title'      => 'Pengolahan Keunggulan',
            'advantages' => $advantages
        ]);
        Response::html($html);
    }

    public function process(Request $request): void
    {
        $process = ProcessModel::getPublished();
        $html = View::renderWithLayout('admin/content/process', 'admin/layout', [
            'title'   => 'Pengolahan Alur Kerja',
            'process' => $process
        ]);
        Response::html($html);
    }

    public function faqs(Request $request): void
    {
        $faqs = FaqModel::getPublished();
        $html = View::renderWithLayout('admin/content/faqs', 'admin/layout', [
            'title' => 'Pengolahan FAQ',
            'faqs'  => $faqs
        ]);
        Response::html($html);
    }

    public function articles(Request $request): void
    {
        $articles = ArticleModel::getPublished();
        $html = View::renderWithLayout('admin/content/articles', 'admin/layout', [
            'title'    => 'Pengolahan Artikel',
            'articles' => $articles
        ]);
        Response::html($html);
    }

    public function stats(Request $request): void
    {
        $stats = StatModel::getPublished();
        $html = View::renderWithLayout('admin/content/stats', 'admin/layout', [
            'title' => 'Pengolahan Statistik & Penanda Verifikasi',
            'stats' => $stats
        ]);
        Response::html($html);
    }
}
