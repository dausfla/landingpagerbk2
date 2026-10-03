<?php
$src = '/Users/firdaus/.gemini/antigravity-ide/brain/e8e2e3b7-4ecb-4e08-9776-1ba6d5424ebc/media__1790870779920.png';
$destDir = __DIR__ . '/../public/assets/img';
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

copy($src, $destDir . '/logo-rbk.png');

$info = getimagesize($src);
echo "Original Dimensions: " . $info[0] . "x" . $info[1] . "\n";

// Create dark background (white text) version of the logo
$im = imagecreatefrompng($src);
$w = imagesx($im);
$h = imagesy($im);

$whiteIm = imagecreatetruecolor($w, $h);
imagealphablending($whiteIm, false);
imagesavealpha($whiteIm, true);
$transparent = imagecolorallocatealpha($whiteIm, 0, 0, 0, 127);
imagefill($whiteIm, 0, 0, $transparent);

for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $colors = imagecolorsforindex($im, $rgb);
        $alpha = $colors['alpha'];
        $r = $colors['red'];
        $g = $colors['green'];
        $b = $colors['blue'];
        
        if ($alpha > 120) {
            continue;
        }
        
        // If the color is dark/black (text), turn it to crisp white
        if ($r < 80 && $g < 80 && $b < 80) {
            $newCol = imagecolorallocatealpha($whiteIm, 255, 255, 255, $alpha);
        } else {
            // Retain the orange house icon
            $newCol = imagecolorallocatealpha($whiteIm, $r, $g, $b, $alpha);
        }
        imagesetpixel($whiteIm, $x, $y, $newCol);
    }
}

imagepng($whiteIm, $destDir . '/logo-rbk-white.png');
imagedestroy($im);
imagedestroy($whiteIm);

echo "Logos successfully saved to {$destDir}/logo-rbk.png and {$destDir}/logo-rbk-white.png\n";
