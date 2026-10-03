<?php
/**
 * Script to generate high-quality placeholder images for RBK Studio landing page.
 * Run using: /Applications/XAMPP/xamppfiles/bin/php scripts/generate_portfolio_images.php
 */

$imgDir = __DIR__ . '/../public/assets/img';
if (!is_dir($imgDir)) {
    mkdir($imgDir, 0755, true);
}

$images = [
    'la-bella.webp' => ['title' => 'La Bella Office & Warehouse', 'type' => 'Kompresif / Perkantoran', 'w' => 1200, 'h' => 750, 'bg' => [15, 14, 13], 'accent' => [221, 92, 62]],
    'arsya.webp' => ['title' => 'Arsya House - Desain Tropis Bogor', 'type' => 'Hunian Tropis Modern', 'w' => 960, 'h' => 600, 'bg' => [245, 245, 243], 'accent' => [221, 92, 62]],
    'rumah-a-before.webp' => ['title' => 'Rumah A - Sebelum Renovasi', 'type' => 'Kondisi Bangunan Lama (2018)', 'w' => 960, 'h' => 600, 'bg' => [70, 70, 70], 'accent' => [180, 180, 180]],
    'rumah-a-after.webp' => ['title' => 'Rumah A - Sesudah Renovasi (RBK)', 'type' => 'Desain & Konstruksi Modern', 'w' => 960, 'h' => 600, 'bg' => [15, 14, 13], 'accent' => [221, 92, 62]],
    'renovancy-rumah-mr-putra.webp' => ['title' => 'RenoVancy Rumah Mr. Putra', 'type' => 'Renovasi Total · Bogor', 'w' => 800, 'h' => 500, 'bg' => [25, 25, 25], 'accent' => [221, 92, 62]],
    'renovancy-rumah-mrs-sela.webp' => ['title' => 'RenoVancy Rumah Mrs. Sela', 'type' => 'Rooftop & Fasad · Bogor', 'w' => 800, 'h' => 500, 'bg' => [35, 35, 35], 'accent' => [221, 92, 62]],
    'ruko-cigiringsing.webp' => ['title' => 'Ruko Cigiringsing', 'type' => 'Commercial Build · Bogor', 'w' => 800, 'h' => 500, 'bg' => [20, 20, 20], 'accent' => [221, 92, 62]],
    'casa-nawasena-cluster-kost.webp' => ['title' => 'Casa Nawasena Cluster Kost', 'type' => 'Kawasan Kost · Dramaga', 'w' => 800, 'h' => 500, 'bg' => [30, 30, 30], 'accent' => [221, 92, 62]],
    'chillax-kost-dramaga.webp' => ['title' => 'Chillax Kost Dramaga', 'type' => 'Kost Eksklusif · Dramaga', 'w' => 800, 'h' => 500, 'bg' => [18, 18, 18], 'accent' => [221, 92, 62]],
    'ab-house.webp' => ['title' => 'AB House', 'type' => 'Studio Desain 2 Lantai', 'w' => 800, 'h' => 500, 'bg' => [40, 40, 40], 'accent' => [221, 92, 62]],
    'arsya-house.webp' => ['title' => 'Arsya House', 'type' => 'Desain Modern 1 Lantai', 'w' => 800, 'h' => 500, 'bg' => [28, 28, 28], 'accent' => [221, 92, 62]],
    'ar-house.webp' => ['title' => 'AR\' House', 'type' => 'Desain Sirkulasi Tropis', 'w' => 800, 'h' => 500, 'bg' => [22, 22, 22], 'accent' => [221, 92, 62]],
    'la-bella-office-warehouse.webp' => ['title' => 'La Bella Office & Warehouse', 'type' => 'Design & Build Perkantoran', 'w' => 800, 'h' => 500, 'bg' => [15, 14, 13], 'accent' => [221, 92, 62]],
    'sinergy-office-warehouse.webp' => ['title' => 'Sinergy Office & Warehouse', 'type' => 'Fasilitas Pergudangan', 'w' => 800, 'h' => 500, 'bg' => [32, 32, 32], 'accent' => [221, 92, 62]],
];

foreach ($images as $filename => $info) {
    $width = $info['w'];
    $height = $info['h'];
    
    $im = imagecreatetruecolor($width, $height);
    
    // Fill background
    $bgColor = imagecolorallocate($im, $info['bg'][0], $info['bg'][1], $info['bg'][2]);
    imagefill($im, 0, 0, $bgColor);
    
    // Draw subtle grid / architectural lines
    $gridColor = imagecolorallocatealpha($im, 255, 255, 255, 115);
    for ($x = 0; $x < $width; $x += 50) {
        imageline($im, $x, 0, $x, $height, $gridColor);
    }
    for ($y = 0; $y < $height; $y += 50) {
        imageline($im, 0, $y, $width, $y, $gridColor);
    }
    
    // Accent banner / geometric architectural block
    $accentColor = imagecolorallocate($im, $info['accent'][0], $info['accent'][1], $info['accent'][2]);
    imagefilledrectangle($im, 40, $height - 110, $width - 40, $height - 30, $accentColor);

    // Text box header
    $whiteColor = imagecolorallocate($im, 255, 255, 255);
    $darkTextColor = imagecolorallocate($im, 15, 14, 13);
    
    // Draw RBK logo badge top left
    imagefilledrectangle($im, 40, 40, 220, 85, $accentColor);
    imagestring($im, 4, 55, 53, "RBK STUDIO", $darkTextColor);
    
    // Draw Title & Type text
    imagestring($im, 5, 55, $height - 95, $info['title'], $darkTextColor);
    imagestring($im, 4, 55, $height - 70, $info['type'], $darkTextColor);
    
    // Save as PNG
    $filePath = $imgDir . '/' . $filename;
    imagepng($im, $filePath, 6);
    imagedestroy($im);
    echo "Generated: {$filename} ({$width}x{$height})\n";
}

echo "All images generated successfully in {$imgDir}.\n";
