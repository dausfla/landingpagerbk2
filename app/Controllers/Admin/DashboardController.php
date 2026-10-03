<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\SettingModel;

class DashboardController
{
    public function index(Request $request): void
    {
        // 1. KPI Stats
        $todayLeads = DB::fetchOne("SELECT COUNT(*) AS total FROM leads WHERE DATE(created_at) = CURDATE() AND deleted_at IS NULL")['total'] ?? 0;
        $sevenDaysLeads = DB::fetchOne("SELECT COUNT(*) AS total FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND deleted_at IS NULL")['total'] ?? 0;
        $thirtyDaysLeads = DB::fetchOne("SELECT COUNT(*) AS total FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND deleted_at IS NULL")['total'] ?? 0;
        
        $completeCount = DB::fetchOne("SELECT COUNT(*) AS total FROM leads WHERE is_complete = 1 AND deleted_at IS NULL")['total'] ?? 0;
        $totalLeads = DB::fetchOne("SELECT COUNT(*) AS total FROM leads WHERE deleted_at IS NULL")['total'] ?? 0;
        $completePercent = $totalLeads > 0 ? round(($completeCount / $totalLeads) * 100) : 0;

        $waClicksToday = DB::fetchOne("SELECT COUNT(*) AS total FROM wa_clicks WHERE DATE(created_at) = CURDATE()")['total'] ?? 0;

        // Calculate Median First Response Time (in minutes) for contacted leads
        $responseTimes = DB::fetchAll(
            "SELECT TIMESTAMPDIFF(MINUTE, created_at, first_contacted_at) AS mins 
             FROM leads 
             WHERE first_contacted_at IS NOT NULL AND deleted_at IS NULL 
             ORDER BY mins ASC"
        );
        $medianResponseMin = 0;
        if (!empty($responseTimes)) {
            $count = count($responseTimes);
            $middle = floor($count / 2);
            if ($count % 2 == 0) {
                $medianResponseMin = round(($responseTimes[$middle - 1]['mins'] + $responseTimes[$middle]['mins']) / 2);
            } else {
                $medianResponseMin = (int)$responseTimes[$middle]['mins'];
            }
        }

        // 2. Go-Live Checklist Data
        $unverifiedStats = DB::fetchOne("SELECT COUNT(*) AS total FROM stats WHERE is_verified = 0 AND deleted_at IS NULL")['total'] ?? 0;
        $publishedTestimonials = 0;
        
        $settings = SettingModel::getAll();
        $missingTracking = empty($settings['gtm_id']) && empty($settings['ga4_measurement_id']) && empty($settings['meta_pixel_id']);
        
        $currentUser = Auth::user();
        $mustChangePass = $currentUser['must_change_password'] ?? false;

        $checklist = [
            'unverified_claims'    => (int)$unverifiedStats,
            'missing_tracking'     => $missingTracking,
            'testimonials_count'   => (int)$publishedTestimonials,
            'must_change_password' => $mustChangePass,
            'privacy_reviewed'     => false, // Flag for admin review
            'smtp_tested'          => !empty($settings['smtp_host']),
        ];

        // 3. Recent 10 Leads
        $recentLeads = DB::fetchAll("SELECT * FROM leads WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 10");

        // 4. Overdue Follow-ups
        $overdueLeads = DB::fetchAll("SELECT * FROM leads WHERE follow_up_at IS NOT NULL AND follow_up_at <= NOW() AND status NOT IN ('deal', 'batal') AND deleted_at IS NULL ORDER BY follow_up_at ASC");

        $html = View::renderWithLayout('admin/dashboard', 'admin/layout', [
            'title'             => 'Beranda Dashboard',
            'todayLeads'        => $todayLeads,
            'sevenDaysLeads'    => $sevenDaysLeads,
            'thirtyDaysLeads'   => $thirtyDaysLeads,
            'completePercent'   => $completePercent,
            'waClicksToday'     => $waClicksToday,
            'medianResponseMin' => $medianResponseMin,
            'checklist'         => $checklist,
            'recentLeads'       => $recentLeads,
            'overdueLeads'      => $overdueLeads
        ]);

        Response::html($html);
    }
}
