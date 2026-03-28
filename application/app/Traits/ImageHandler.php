<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ImageHandler
{
    /**
     * Handle image upload and apply watermark.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  string  $disk
     * @param  string|null  $filename
     * @param  string|null  $watermarkPath
     * @return string
     */
    public function uploadAndWatermark(UploadedFile $file, string $path, string $disk = 'public', ?string $filename = null, ?string $watermarkPath = null): string
    {
        $filename = $filename ?? time() . '_' . $file->getClientOriginalName();
        $tempPath = $file->getRealPath();

        // Load original image
        $image = $this->createImageFromPath($tempPath);
        if (!$image) {
            // Fallback to normal upload if GD fails or file type not supported for watermarking
            return $file->storeAs($path, $filename, $disk);
        }

        // Apply watermark
        $watermarkPath = $watermarkPath ?? public_path('assets/img/favicon/apple-touch-icon.png');
        if (file_exists($watermarkPath)) {
            $this->applyWatermark($image, $watermarkPath);
        } else {
            $this->applyTextWatermark($image, config('app.name', 'POS Laravel'));
        }

        // Save to temporary buffer
        ob_start();
        $extension = strtolower($file->getClientOriginalExtension());
        
        switch ($extension) {
            case 'png':
                imagepng($image);
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    imagewebp($image);
                } else {
                    imagejpeg($image, null, 90);
                }
                break;
            case 'gif':
                imagegif($image);
                break;
            default:
                imagejpeg($image, null, 90);
                break;
        }
        
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Store using Laravel Storage
        $filePath = ($path ? $path . '/' : '') . $filename;
        Storage::disk($disk)->put($filePath, $imageData);

        return $filePath;
    }

    /**
     * Create image resource from path based on file type.
     */
    protected function createImageFromPath($path)
    {
        $info = @getimagesize($path);
        if (!$info) return null;

        return match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : null,
            default => null,
        };
    }

    /**
     * Apply image watermark.
     */
    protected function applyWatermark(&$image, string $watermarkPath)
    {
        $watermarkInfo = @getimagesize($watermarkPath);
        if (!$watermarkInfo) return;

        $watermark = match ($watermarkInfo[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($watermarkPath),
            IMAGETYPE_PNG => imagecreatefrompng($watermarkPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($watermarkPath) : null,
            default => null,
        };

        if (!$watermark) return;

        $imgW = imagesx($image);
        $imgH = imagesy($image);
        $wtW = imagesx($watermark);
        $wtH = imagesy($watermark);

        // Resize watermark if too big (max 25% of image width)
        $maxWtW = $imgW * 0.25;
        if ($wtW > $maxWtW) {
            $newWtW = $maxWtW;
            $newWtH = $wtH * ($newWtW / $wtW);
            $newWatermark = imagecreatetruecolor($newWtW, $newWtH);
            
            // Handle transparency for PNG/WebP
            imagealphablending($newWatermark, false);
            imagesavealpha($newWatermark, true);
            
            imagecopyresampled($newWatermark, $watermark, 0, 0, 0, 0, $newWtW, $newWtH, $wtW, $wtH);
            imagedestroy($watermark);
            $watermark = $newWatermark;
            $wtW = $newWtW;
            $wtH = $newWtH;
        }

        // Position: Bottom Right with 10px padding
        $posX = $imgW - $wtW - 20;
        $posY = $imgH - $wtH - 20;

        // Set transparency if it's a PNG/WebP watermark
        imagealphablending($image, true);
        imagecopy($image, $watermark, $posX, $posY, 0, 0, $wtW, $wtH);
        
        imagedestroy($watermark);
    }

    /**
     * Apply text watermark as fallback.
     */
    protected function applyTextWatermark(&$image, string $text)
    {
        $imgW = imagesx($image);
        $imgH = imagesy($image);
        
        $fontSize = max(10, $imgW / 30);
        $color = imagecolorallocatealpha($image, 255, 255, 255, 60); // Semi-transparent white
        
        // Simple text watermark (bottom right)
        // Note: For better text we'd use imagettftext, but that requires a font file path.
        // imagestring is built-in but limited.
        imagestring($image, 5, $imgW - (strlen($text) * 10) - 20, $imgH - 30, $text, $color);
    }
}
