<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\LeadModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class LeadAdminController
{
    public function index(Request $request): void
    {
        $status = $request->get('status');
        $need = $request->get('need');
        $search = trim($request->get('search', ''));
        $pic = $request->get('assigned_to');

        $where = ["l.deleted_at IS NULL"];
        $params = [];

        if (!empty($status)) {
            $where[] = "l.status = ?";
            $params[] = $status;
        }

        if (!empty($need)) {
            $where[] = "l.need = ?";
            $params[] = $need;
        }

        if (!empty($pic)) {
            $where[] = "l.assigned_to = ?";
            $params[] = (int)$pic;
        }

        if (!empty($search)) {
            $where[] = "(l.code LIKE ? OR l.name LIKE ? OR l.phone LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT l.*, u.name AS pic_name
                FROM leads l
                LEFT JOIN users u ON l.assigned_to = u.id
                WHERE {$whereClause}
                ORDER BY l.created_at DESC";

        $leads = DB::fetchAll($sql, $params);
        $users = UserModel::all();

        $html = View::renderWithLayout('admin/leads/index', 'admin/layout', [
            'title'  => 'Manajemen Data Leads',
            'leads'  => $leads,
            'users'  => $users,
            'status' => $status,
            'need'   => $need,
            'search' => $search,
            'pic'    => $pic
        ]);

        Response::html($html);
    }

    public function detail(Request $request, string $id): void
    {
        $leadId = (int)$id;
        $lead = LeadModel::findById($leadId);

        if (!$lead) {
            Response::redirect('/admin/leads');
            return;
        }

        $activities = DB::fetchAll(
            "SELECT a.*, u.name AS user_name 
             FROM lead_activities a 
             LEFT JOIN users u ON a.user_id = u.id 
             WHERE a.lead_id = ? 
             ORDER BY a.created_at DESC",
            [$leadId]
        );

        $users = UserModel::all();

        $html = View::renderWithLayout('admin/leads/detail', 'admin/layout', [
            'title'      => "Detail Lead: {$lead['code']}",
            'lead'       => $lead,
            'activities' => $activities,
            'users'      => $users,
            'success'    => $request->get('success')
        ]);

        Response::html($html);
    }

    public function updateStatus(Request $request, string $id): void
    {
        $leadId = (int)$id;
        $lead = LeadModel::findById($leadId);
        if (!$lead) {
            Response::json(['success' => false, 'message' => 'Lead tidak ditemukan.'], 404);
            return;
        }

        $newStatus = $request->post('status');
        $lostReason = $request->post('lost_reason');
        $note = trim($request->post('note', ''));
        $assignedTo = !empty($request->post('assigned_to')) ? (int)$request->post('assigned_to') : null;
        $followUpAt = !empty($request->post('follow_up_at')) ? $request->post('follow_up_at') : null;
        $estimatedValue = !empty($request->post('estimated_value')) ? (int)$request->post('estimated_value') : null;

        $oldStatus = $lead['status'];

        // Automatic first_contacted_at recording if status changes from 'baru'
        $firstContactedAt = $lead['first_contacted_at'];
        if ($oldStatus === 'baru' && $newStatus !== 'baru' && $firstContactedAt === null) {
            $firstContactedAt = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE leads SET
                status = ?,
                lost_reason = ?,
                assigned_to = ?,
                first_contacted_at = ?,
                follow_up_at = ?,
                estimated_value = ?,
                updated_at = NOW()
                WHERE id = ?";

        DB::query($sql, [
            $newStatus,
            $newStatus === 'batal' ? $lostReason : null,
            $assignedTo,
            $firstContactedAt,
            $followUpAt,
            $estimatedValue,
            $leadId
        ]);

        // Record Lead Activity
        DB::query(
            "INSERT INTO lead_activities (lead_id, user_id, type, from_status, to_status, note, created_at)
             VALUES (?, ?, 'status_change', ?, ?, ?, NOW())",
            [$leadId, Auth::id(), $oldStatus, $newStatus, $note]
        );

        ActivityLogModel::log('update_lead_status', 'leads', $leadId, ['status' => $oldStatus], ['status' => $newStatus], Auth::id(), $request->getIpHash());

        Response::redirect("/admin/leads/detail/{$leadId}?success=1");
    }

    public function exportCsv(Request $request): void
    {
        $leads = DB::fetchAll("SELECT * FROM leads WHERE deleted_at IS NULL ORDER BY created_at DESC");

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=leads_rbk_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Kode', 'Tanggal', 'Nama', 'WhatsApp', 'Kebutuhan', 'Lokasi', 'Luas Tanah', 'Luas Bangunan', 'Budget', 'Status', 'Alasan Batal', 'First Contacted', 'UTM Source', 'UTM Medium']);

        foreach ($leads as $l) {
            fputcsv($output, [
                $l['code'],
                $l['created_at'],
                $l['name'],
                $l['phone'],
                $l['need'],
                $l['location'],
                $l['land_size'],
                $l['building_size_m2'],
                $l['budget_range'],
                $l['status'],
                $l['lost_reason'],
                $l['first_contacted_at'],
                $l['utm_source'],
                $l['utm_medium']
            ]);
        }

        fclose($output);
        exit;
    }
}
