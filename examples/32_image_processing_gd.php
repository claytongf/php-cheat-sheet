<?php
/**
 * PHP Cheat Sheet - 32: Dynamic Image Processing with GD
 * 
 * Topics covered:
 * - Checking and enabling the 'gd' graphics extension
 * - Creating blank canvases and color allocations
 * - Drawing basic shapes: rectangles, lines, arcs, ellipses
 * - Adding texts to images (built-in fonts vs TrueType TTF fonts)
 * - Image resampling, scaling, and cropping (Thumbnail creation)
 * - Saving images (PNG, JPEG) or outputting as Base64 data URIs
 */

echo "=== 1. CHECKING FOR THE GD EXTENSION ===\n";
if (!extension_loaded('gd')) {
    echo "The GD extension is not loaded in this PHP environment.\n";
    echo "To install on Linux: apt-get install php-gd\n";
    echo "To enable on Windows: uncomment 'extension=gd' in php.ini\n\n";
    echo "[Simulation Mode Enabled] Demonstrating GD API code patterns without running active GD resource handles...\n\n";
    $gdLoaded = false;
} else {
    echo "GD library is loaded. Generating dynamic images...\n\n";
    $gdLoaded = true;
}

echo "=== 2. CREATING A BLANK IMAGE AND DRAWING SHAPES ===\n";

if ($gdLoaded) {
    // 1. Create a true color image canvas (width: 400, height: 150)
    $image = imagecreatetruecolor(400, 150);
    
    // 2. Allocate colors (RGB format)
    // The first allocated color becomes the default background fill for imagecreatetruecolor
    $bg = imagecolorallocate($image, 18, 14, 38); // Dark violet background
    $accent = imagecolorallocate($image, 143, 95, 232); // Glowing violet
    $secondary = imagecolorallocate($image, 0, 242, 254); // Cyan
    $white = imagecolorallocate($image, 255, 255, 255);
    
    // 3. Clear/fill canvas with the background
    imagefill($image, 0, 0, $bg);
    
    // 4. Draw shapes
    // Draw an outlined rectangle
    imagerectangle($image, 10, 10, 390, 140, $accent);
    // Draw a filled circle (ellipse with matching width & height)
    imagefilledellipse($image, 80, 75, 80, 80, $secondary);
    // Draw a line
    imageline($image, 140, 75, 380, 75, $white);
    
    // 5. Draw text using built-in font (font size ranges 1-5)
    imagestring($image, 5, 150, 45, "PHP GD Library Example", $white);
    imagestring($image, 3, 150, 85, "Shapes & Canvas Graphics", $accent);
    
    // 6. Capture image output buffer to base64
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    
    // Free memory from GD resource (deprecated in PHP 8.5 as GdImage destroys automatically)
    if (PHP_VERSION_ID < 80500) {
        imagedestroy($image);
    }
    
    echo "Generated Canvas image size: 400x150 pixels\n";
    echo "Output PNG representation (Base64 data URI):\n";
    echo "data:image/png;base64," . base64_encode($imageData) . "\n\n";
} else {
    // Mock code demonstration
    $codeSnippet = '
// Create a 400x150 canvas
$img = imagecreatetruecolor(400, 150);
$bgColor = imagecolorallocate($img, 18, 14, 38);
imagefill($img, 0, 0, $bgColor);

// Draw circle and lines
$cyan = imagecolorallocate($img, 0, 242, 254);
imagefilledellipse($img, 80, 75, 80, 80, $cyan);

// Output PNG
header("Content-Type: image/png");
imagepng($img);
if (PHP_VERSION_ID < 80500) {
    imagedestroy($img);
}
';
    echo "Canvas Drawing Code Pattern:\n" . trim($codeSnippet) . "\n\n";
}


echo "=== 3. CREATING THUMBNAILS (RESAMPLING AND SCALING) ===\n";
echo "How to resize a high-resolution image to a thumbnail securely:\n\n";

if ($gdLoaded) {
    // Create a mock source image to represent a loaded file
    $sourceImg = imagecreatetruecolor(800, 600);
    $red = imagecolorallocate($sourceImg, 255, 0, 0);
    imagefill($sourceImg, 0, 0, $red); // Mock red photo
    
    $srcWidth = imagesx($sourceImg);
    $srcHeight = imagesy($sourceImg);
    
    // Target thumbnail size
    $thumbWidth = 150;
    $thumbHeight = 112; // Maintaining 4:3 aspect ratio
    
    // Create blank thumbnail canvas
    $thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
    // Keep transparent backgrounds if resizing PNGs
    imagealphablending($thumbImg, false);
    imagesavealpha($thumbImg, true);
    
    // High-quality resample (Copy and resize source into destination canvas)
    imagecopyresampled(
        $thumbImg,      // Destination canvas
        $sourceImg,     // Source image handle
        0, 0,           // Destination coordinates (x, y)
        0, 0,           // Source coordinates (x, y)
        $thumbWidth,    // Destination width
        $thumbHeight,   // Destination height
        $srcWidth,      // Source width
        $srcHeight      // Source height
    );
    
    // Save thumbnail
    $tempThumbPath = sys_get_temp_dir() . '/thumb_output.jpg';
    imagejpeg($thumbImg, $tempThumbPath, 85); // 85% quality quality compression
    
    echo "Successfully resampled image from {$srcWidth}x{$srcHeight} down to {$thumbWidth}x{$thumbHeight}.\n";
    echo "Saved thumbnail locally at: $tempThumbPath\n";
    
    // Clean handles (deprecated in PHP 8.5 as GdImage destroys automatically)
    if (PHP_VERSION_ID < 80500) {
        imagedestroy($sourceImg);
        imagedestroy($thumbImg);
    }
    @unlink($tempThumbPath);
} else {
    $resizeSnippet = '
function createThumbnail($srcPath, $destPath, $targetWidth = 150) {
    // 1. Detect image file format and load correct resource
    $info = getimagesize($srcPath);
    switch ($info["mime"]) {
        case "image/jpeg": $src = imagecreatefromjpeg($srcPath); break;
        case "image/png":  $src = imagecreatefrompng($srcPath);  break;
        case "image/gif":  $src = imagecreatefromgif($srcPath);  break;
        default: return false; // Unsupported type
    }
    
    $srcW = imagesx($src);
    $srcH = imagesy($src);
    
    // 2. Maintain aspect ratio
    $targetHeight = floor($srcH * ($targetWidth / $srcW));
    
    // 3. Resample
    $dst = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcW, $srcH);
    
    // 4. Save and output
    imagejpeg($dst, $destPath, 90); // 90% quality JPEG
    
    if (PHP_VERSION_ID < 80500) {
        imagedestroy($src);
        imagedestroy($dst);
    }
    return true;
}
';
    echo "Generic Resizing Function Pattern:\n" . trim($resizeSnippet) . "\n";
}
?>
