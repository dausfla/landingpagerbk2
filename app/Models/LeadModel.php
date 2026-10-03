<?php

namespace App\Models;

use App\Core\DB;

class LeadModel
{
    public static function createStep1(array $data): array
    {
        // Generate lead code: RBK-YYMMDD-XXXX
        $dateStr = date('ymd');
        $randomSeq = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        $code = "RBK-{$dateStr}-{$randomSeq}";

        // Normalize Phone Number (08... / +62... -> 62...)
        $phoneNorm = preg_replace('/[^0-9]/', '', $data['phone']);
        if (str_starts_with($phoneNorm, '0')) {
            $phoneNorm = '62' . substr($phoneNorm, 1);
        }

        // Check duplicate within 30 days
        $dup = DB::fetchOne(
            "SELECT id FROM leads WHERE phone_normalized = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND deleted_at IS NULL",
            [$phoneNorm]
        );

        $isDuplicate = $dup ? 1 : 0;
        $duplicateOf = $dup ? (int)$dup['id'] : null;

        $sql = "INSERT INTO leads (code, name, phone, phone_normalized, need, is_complete, status, is_duplicate, duplicate_of, ip_hash, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, 0, 'baru', ?, ?, ?, ?, NOW())";

        $leadId = DB::insert($sql, [
            $code,
            $data['name'],
            $data['phone'],
            $phoneNorm,
            $data['need'],
            $isDuplicate,
            $duplicateOf,
            $data['ip_hash'],
            $data['user_agent']
        ]);

        return [
            'id'   => (int)$leadId,
            'code' => $code
        ];
    }

    public static function updateStep2(int $id, array $data): bool
    {
        $sql = "UPDATE leads SET
                location = ?,
                land_size = ?,
                building_size_m2 = ?,
                budget_range = ?,
                package_choice = ?,
                notes = ?,
                consent_at = NOW(),
                is_complete = 1,
                calc_snapshot = ?,
                utm_source = ?,
                utm_medium = ?,
                utm_campaign = ?,
                utm_term = ?,
                utm_content = ?,
                gclid = ?,
                fbclid = ?,
                ttclid = ?,
                referrer = ?,
                landing_url = ?,
                first_touch = ?,
                device = ?,
                updated_at = NOW()
                WHERE id = ?";

        return DB::query($sql, [
            $data['location'] ?? null,
            $data['land_size'] ?? null,
            !empty($data['building_size_m2']) ? (int)$data['building_size_m2'] : null,
            $data['budget_range'] ?? null,
            $data['package_choice'] ?? null,
            $data['notes'] ?? null,
            !empty($data['calc_snapshot']) ? json_encode($data['calc_snapshot'], JSON_UNESCAPED_UNICODE) : null,
            $data['utm_source'] ?? null,
            $data['utm_medium'] ?? null,
            $data['utm_campaign'] ?? null,
            $data['utm_term'] ?? null,
            $data['utm_content'] ?? null,
            $data['gclid'] ?? null,
            $data['fbclid'] ?? null,
            $data['ttclid'] ?? null,
            $data['referrer'] ?? null,
            $data['landing_url'] ?? null,
            !empty($data['first_touch']) ? json_encode($data['first_touch'], JSON_UNESCAPED_UNICODE) : null,
            $data['device'] ?? null,
            $id
        ]);
    }

    public static function findByCode(string $code): ?array
    {
        return DB::fetchOne("SELECT * FROM leads WHERE code = ? AND deleted_at IS NULL", [$code]);
    }

    public static function findById(int $id): ?array
    {
        return DB::fetchOne("SELECT * FROM leads WHERE id = ? AND deleted_at IS NULL", [$id]);
    }
}
