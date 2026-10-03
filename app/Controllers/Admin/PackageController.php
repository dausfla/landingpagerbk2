<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Cache;
use App\Models\PackageModel;
use App\Models\ActivityLogModel;

class PackageController
{
    public function index(Request $request): void
    {
        $packages = PackageModel::getPublishedPackages();
        $html = View::renderWithLayout('admin/packages/index', 'admin/layout', [
            'title'    => 'Pengolahan Paket & Harga',
            'packages' => $packages
        ]);
        Response::html($html);
    }

    public function create(Request $request): void
    {
        $html = View::renderWithLayout('admin/packages/form', 'admin/layout', [
            'title'   => 'Tambah Paket Baru',
            'package' => null,
            'error'   => null
        ]);
        Response::html($html);
    }

    public function store(Request $request): void
    {
        $serviceType = $request->post('service_type');
        $name = trim($request->post('name', ''));
        $priceMin = (int)$request->post('price_min');
        $priceMax = !empty($request->post('price_max')) ? (int)$request->post('price_max') : null;

        // Validation: price_min <= price_max
        if ($priceMax !== null && $priceMin > $priceMax) {
            Response::html(View::renderWithLayout('admin/packages/form', 'admin/layout', [
                'title'   => 'Tambah Paket Baru',
                'package' => $_POST,
                'error'   => 'Harga minimal tidak boleh lebih besar dari harga maksimal.'
            ]));
            return;
        }

        $sql = "INSERT INTO packages (service_type, name, tagline, price_min, price_max, unit, badge, is_highlighted, description, suitable_for, cta_label, sort_order, is_published, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $pkgId = DB::insert($sql, [
            $serviceType,
            $name,
            $request->post('tagline'),
            $priceMin,
            $priceMax,
            $request->post('unit', '/m²'),
            $request->post('badge'),
            (int)$request->post('is_highlighted', 0),
            $request->post('description'),
            json_encode(array_map('trim', explode(',', $request->post('suitable_for', '')))),
            $request->post('cta_label', 'Pilih Paket'),
            (int)$request->post('sort_order', 0),
            (int)$request->post('is_published', 1)
        ]);

        Cache::clearAll();
        ActivityLogModel::log('create_package', 'packages', (int)$pkgId, null, ['name' => $name], Auth::id(), $request->getIpHash());

        Response::redirect('/admin/packages');
    }

    public function edit(Request $request, string $id): void
    {
        $pkgId = (int)$id;
        $pkg = DB::fetchOne("SELECT * FROM packages WHERE id = ? AND deleted_at IS NULL", [$pkgId]);

        if (!$pkg) {
            Response::redirect('/admin/packages');
            return;
        }

        $specs = DB::fetchAll("SELECT * FROM package_specs WHERE package_id = ? ORDER BY sort_order ASC", [$pkgId]);

        $html = View::renderWithLayout('admin/packages/form', 'admin/layout', [
            'title'   => "Edit Paket: {$pkg['name']}",
            'package' => $pkg,
            'specs'   => $specs,
            'error'   => null
        ]);
        Response::html($html);
    }

    public function update(Request $request, string $id): void
    {
        $pkgId = (int)$id;
        $pkg = DB::fetchOne("SELECT * FROM packages WHERE id = ? AND deleted_at IS NULL", [$pkgId]);

        if (!$pkg) {
            Response::redirect('/admin/packages');
            return;
        }

        $priceMin = (int)$request->post('price_min');
        $priceMax = !empty($request->post('price_max')) ? (int)$request->post('price_max') : null;

        if ($priceMax !== null && $priceMin > $priceMax) {
            Response::html(View::renderWithLayout('admin/packages/form', 'admin/layout', [
                'title'   => "Edit Paket: {$pkg['name']}",
                'package' => array_merge($pkg, $_POST),
                'error'   => 'Harga minimal tidak boleh lebih besar dari harga maksimal.'
            ]));
            return;
        }

        // Log price change to package_price_history if prices changed
        if ($pkg['price_min'] != $priceMin || $pkg['price_max'] != $priceMax) {
            DB::query(
                "INSERT INTO package_price_history (package_id, old_min, old_max, new_min, new_max, changed_by, changed_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())",
                [$pkgId, $pkg['price_min'], $pkg['price_max'], $priceMin, $priceMax, Auth::id()]
            );
        }

        $sql = "UPDATE packages SET
                service_type = ?,
                name = ?,
                tagline = ?,
                price_min = ?,
                price_max = ?,
                unit = ?,
                badge = ?,
                is_highlighted = ?,
                description = ?,
                suitable_for = ?,
                cta_label = ?,
                sort_order = ?,
                is_published = ?,
                updated_at = NOW()
                WHERE id = ?";

        DB::query($sql, [
            $request->post('service_type'),
            trim($request->post('name')),
            $request->post('tagline'),
            $priceMin,
            $priceMax,
            $request->post('unit', '/m²'),
            $request->post('badge'),
            (int)$request->post('is_highlighted', 0),
            $request->post('description'),
            json_encode(array_map('trim', explode(',', $request->post('suitable_for', '')))),
            $request->post('cta_label', 'Pilih Paket'),
            (int)$request->post('sort_order', 0),
            (int)$request->post('is_published', 1),
            $pkgId
        ]);

        Cache::clearAll();
        ActivityLogModel::log('update_package', 'packages', $pkgId, $pkg, ['price_min' => $priceMin], Auth::id(), $request->getIpHash());

        Response::redirect('/admin/packages');
    }

    public function delete(Request $request, string $id): void
    {
        $pkgId = (int)$id;
        $pkg = DB::fetchOne("SELECT * FROM packages WHERE id = ? AND deleted_at IS NULL", [$pkgId]);

        if ($pkg) {
            DB::query("UPDATE packages SET deleted_at = NOW(), is_published = 0 WHERE id = ?", [$pkgId]);
            Cache::clearAll();
            ActivityLogModel::log('delete_package', 'packages', $pkgId, $pkg, null, Auth::id(), $request->getIpHash());
        }

        Response::redirect('/admin/packages');
    }
}
