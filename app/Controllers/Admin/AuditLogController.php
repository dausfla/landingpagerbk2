<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ActivityLogModel;

class AuditLogController
{
    public function index(Request $request): void
    {
        $logs = ActivityLogModel::getLatest(100);

        $html = View::renderWithLayout('admin/activity_logs/index', 'admin/layout', [
            'title' => 'Log Aktivitas Admin',
            'logs'  => $logs
        ]);

        Response::html($html);
    }
}
