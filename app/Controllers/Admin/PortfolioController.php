<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Cache;
use App\Models\PortfolioModel;
use App\Models\ActivityLogModel;

class PortfolioController
{
    public function index(Request $request): void
    {
        $portfolios = DB::fetchAll("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM portfolios p
            JOIN portfolio_categories c ON p.category_id = c.id
            WHERE p.deleted_at IS NULL
            ORDER BY p.sort_order ASC, p.id DESC
        ");
        $categories = PortfolioModel::getCategories();

        $html = View::renderWithLayout('admin/portfolios/index', 'admin/layout', [
            'title'      => 'Pengolahan Portofolio Proyek',
            'portfolios' => $portfolios,
            'categories' => $categories
        ]);

        Response::html($html);
    }

    public function create(Request $request): void
    {
        $categories = PortfolioModel::getCategories();
        $html = View::renderWithLayout('admin/portfolios/form', 'admin/layout', [
            'title'      => 'Tambah Portofolio Proyek',
            'portfolio'  => null,
            'categories' => $categories,
            'error'      => null
        ]);
        Response::html($html);
    }

    public function store(Request $request): void
    {
        $title = trim($request->post('title', ''));
        $slug = trim($request->post('slug', ''));
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        $categoryId = (int)$request->post('category_id');
        $location = trim($request->post('location', 'Bogor'));
        $year = trim($request->post('year', date('Y')));
        $serviceType = $request->post('service_type', 'studio');
        $status = $request->post('status', 'selesai');
        $shortDesc = trim($request->post('short_desc', ''));
        $sortOrder = (int)$request->post('sort_order', 0);
        $isFeatured = (int)$request->post('is_featured', 0);
        $isPublished = (int)$request->post('is_published', 1);

        // Upload Before & After Photos
        $beforeImage = $this->uploadImage('before_image', 'before');
        $afterImage = $this->uploadImage('after_image', 'after');

        $sql = "INSERT INTO portfolios (title, slug, category_id, service_type, location, year, status, short_desc, before_image, after_image, is_featured, sort_order, is_published, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $pId = DB::insert($sql, [
            $title, $slug, $categoryId, $serviceType, $location, $year, $status, $shortDesc, $beforeImage, $afterImage, $isFeatured, $sortOrder, $isPublished
        ]);

        Cache::clearAll();
        ActivityLogModel::log('create_portfolio', 'portfolios', (int)$pId, null, ['title' => $title], Auth::id(), $request->getIpHash());

        Response::redirect('/admin/portfolios');
    }

    public function edit(Request $request, string $id): void
    {
        $pId = (int)$id;
        $portfolio = DB::fetchOne("SELECT * FROM portfolios WHERE id = ? AND deleted_at IS NULL", [$pId]);

        if (!$portfolio) {
            Response::redirect('/admin/portfolios');
            return;
        }

        $categories = PortfolioModel::getCategories();
        $html = View::renderWithLayout('admin/portfolios/form', 'admin/layout', [
            'title'      => "Edit Portofolio: {$portfolio['title']}",
            'portfolio'  => $portfolio,
            'categories' => $categories,
            'error'      => null
        ]);
        Response::html($html);
    }

    public function update(Request $request, string $id): void
    {
        $pId = (int)$id;
        $portfolio = DB::fetchOne("SELECT * FROM portfolios WHERE id = ? AND deleted_at IS NULL", [$pId]);

        if (!$portfolio) {
            Response::redirect('/admin/portfolios');
            return;
        }

        $title = trim($request->post('title', ''));
        $slug = trim($request->post('slug', ''));
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        $categoryId = (int)$request->post('category_id');
        $location = trim($request->post('location', 'Bogor'));
        $year = trim($request->post('year', date('Y')));
        $serviceType = $request->post('service_type', 'studio');
        $status = $request->post('status', 'selesai');
        $shortDesc = trim($request->post('short_desc', ''));
        $sortOrder = (int)$request->post('sort_order', 0);
        $isFeatured = (int)$request->post('is_featured', 0);
        $isPublished = (int)$request->post('is_published', 1);

        // Upload new Before / After Photos if selected
        $uploadedBefore = $this->uploadImage('before_image', 'before');
        $uploadedAfter = $this->uploadImage('after_image', 'after');

        $beforeImage = $uploadedBefore !== null ? $uploadedBefore : ($portfolio['before_image'] ?? null);
        $afterImage = $uploadedAfter !== null ? $uploadedAfter : ($portfolio['after_image'] ?? null);

        $sql = "UPDATE portfolios SET
                title = ?, slug = ?, category_id = ?, service_type = ?, location = ?, year = ?, status = ?,
                short_desc = ?, before_image = ?, after_image = ?, is_featured = ?, sort_order = ?, is_published = ?, updated_at = NOW()
                WHERE id = ?";

        DB::query($sql, [
            $title, $slug, $categoryId, $serviceType, $location, $year, $status, $shortDesc, $beforeImage, $afterImage, $isFeatured, $sortOrder, $isPublished, $pId
        ]);

        Cache::clearAll();
        ActivityLogModel::log('update_portfolio', 'portfolios', $pId, $portfolio, ['title' => $title], Auth::id(), $request->getIpHash());

        Response::redirect('/admin/portfolios');
    }

    public function delete(Request $request, string $id): void
    {
        $pId = (int)$id;
        $portfolio = DB::fetchOne("SELECT * FROM portfolios WHERE id = ? AND deleted_at IS NULL", [$pId]);

        if ($portfolio) {
            DB::query("UPDATE portfolios SET deleted_at = NOW(), is_published = 0 WHERE id = ?", [$pId]);
            Cache::clearAll();
            ActivityLogModel::log('delete_portfolio', 'portfolios', $pId, $portfolio, null, Auth::id(), $request->getIpHash());
        }

        Response::redirect('/admin/portfolios');
    }

    private function uploadImage(string $fieldName, string $prefix): ?string
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fieldName];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowed)) {
            return null;
        }

        $targetDir = __DIR__ . '/../../../public/uploads/portfolios/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $newFilename = $prefix . '_' . time() . '_' . uniqid() . '.' . $ext;
        $targetPath = $targetDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/portfolios/' . $newFilename;
        }

        return null;
    }
}
