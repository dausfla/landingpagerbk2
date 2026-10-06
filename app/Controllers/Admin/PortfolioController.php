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
        $rawSlug = trim($request->post('slug', ''));
        $slug = $this->makeUniqueSlug($title, $rawSlug);

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

        try {
            $pId = DB::insert($sql, [
                $title, $slug, $categoryId, $serviceType, $location, $year, $status, $shortDesc, $beforeImage, $afterImage, $isFeatured, $sortOrder, $isPublished
            ]);

            Cache::clearAll();
            ActivityLogModel::log('create_portfolio', 'portfolios', (int)$pId, null, ['title' => $title], Auth::id(), $request->getIpHash());

            Response::redirect('/admin/portfolios');
        } catch (\Throwable $e) {
            $categories = PortfolioModel::getCategories();
            $userMsg = 'Gagal menyimpan portofolio. Silakan periksa kembali data Anda.';
            if (strpos($e->getMessage(), '1062') !== false || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $userMsg = 'Judul atau Slug URL portofolio sudah digunakan. Silakan ubah judul atau slug proyek.';
            }

            $html = View::renderWithLayout('admin/portfolios/form', 'admin/layout', [
                'title'      => 'Tambah Portofolio Proyek',
                'portfolio'  => $_POST,
                'categories' => $categories,
                'error'      => $userMsg
            ]);
            Response::html($html);
        }
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
        $rawSlug = trim($request->post('slug', ''));
        $slug = $this->makeUniqueSlug($title, $rawSlug, $pId);

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

        try {
            DB::query($sql, [
                $title, $slug, $categoryId, $serviceType, $location, $year, $status, $shortDesc, $beforeImage, $afterImage, $isFeatured, $sortOrder, $isPublished, $pId
            ]);

            Cache::clearAll();
            ActivityLogModel::log('update_portfolio', 'portfolios', $pId, $portfolio, ['title' => $title], Auth::id(), $request->getIpHash());

            Response::redirect('/admin/portfolios');
        } catch (\Throwable $e) {
            $categories = PortfolioModel::getCategories();
            $userMsg = 'Gagal memperbarui portofolio. Silakan periksa kembali data Anda.';
            if (strpos($e->getMessage(), '1062') !== false || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $userMsg = 'Judul atau Slug URL portofolio sudah digunakan. Silakan ubah judul atau slug proyek.';
            }

            $html = View::renderWithLayout('admin/portfolios/form', 'admin/layout', [
                'title'      => "Edit Portofolio: {$portfolio['title']}",
                'portfolio'  => array_merge($portfolio, $_POST),
                'categories' => $categories,
                'error'      => $userMsg
            ]);
            Response::html($html);
        }
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

    private function makeUniqueSlug(string $title, string $rawSlug, ?int $currentId = null): string
    {
        $slug = trim($rawSlug);
        if (empty($slug)) {
            $slug = $title;
        }

        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');

        if (empty($slug)) {
            $slug = 'proyek-' . time();
        }

        $baseSlug = $slug;
        $counter = 1;

        while (true) {
            // Query table-wide (including soft-deleted rows) because MySQL UNIQUE index applies to whole table
            $sql = "SELECT id, deleted_at FROM portfolios WHERE slug = ?";
            $params = [$slug];

            if ($currentId !== null) {
                $sql .= " AND id != ?";
                $params[] = $currentId;
            }

            $existing = DB::fetchOne($sql, $params);
            if (!$existing) {
                break;
            }

            // If the matching slug is on a soft-deleted portfolio, rename the old soft-deleted row's slug to free up baseSlug
            if (!empty($existing['deleted_at'])) {
                DB::query("UPDATE portfolios SET slug = CONCAT(slug, '-deleted-', UNIX_TIMESTAMP()) WHERE id = ?", [$existing['id']]);
                continue;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
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
            $this->optimizeImage($targetPath, $ext);
            return '/uploads/portfolios/' . $newFilename;
        }

        return null;
    }

    private function optimizeImage(string $filePath, string $ext): void
    {
        if (!function_exists('imagecreatefromstring')) {
            return;
        }

        $info = @getimagesize($filePath);
        if (!$info) return;

        [$width, $height] = $info;
        $maxDimension = 1920; // Max width/height in pixels

        // If dimensions are within bounds and filesize is small (< 500KB), keep as is
        if ($width <= $maxDimension && $height <= $maxDimension && filesize($filePath) < 500000) {
            return;
        }

        $srcImg = @imagecreatefromstring(file_get_contents($filePath));
        if (!$srcImg) return;

        // Calculate new dimensions preserving aspect ratio
        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width >= $height) {
                $newWidth = $maxDimension;
                $newHeight = (int)round(($height / $width) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int)round(($width / $height) * $maxDimension);
            }

            $dstImg = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve PNG transparency
            if (in_array($ext, ['png', 'webp', 'gif'])) {
                imagealphablending($dstImg, false);
                imagesavealpha($dstImg, true);
                $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
                imagefilledrectangle($dstImg, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($srcImg);
            $srcImg = $dstImg;
        }

        // Save compressed image back to disk
        if ($ext === 'png') {
            imagepng($srcImg, $filePath, 6);
        } else if (in_array($ext, ['jpg', 'jpeg'])) {
            imagejpeg($srcImg, $filePath, 82);
        } else if ($ext === 'webp' && function_exists('imagewebp')) {
            imagewebp($srcImg, $filePath, 82);
        }

        imagedestroy($srcImg);
    }
}
