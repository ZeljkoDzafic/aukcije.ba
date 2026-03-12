<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Image Optimization Service
 * 
 * Handles image upload, optimization, and CDN delivery
 * 
 * Features:
 * - Resize to multiple sizes
 * - Convert to WebP
 * - Generate blurhash placeholder
 * - Upload to S3/CDN
 * - Cache optimization
 */
class ImageOptimizationService
{
    /**
     * Image size presets
     */
    protected array $sizes = [
        'thumbnail' => ['width' => 400, 'height' => 400, 'quality' => 80],
        'medium' => ['width' => 800, 'height' => 800, 'quality' => 85],
        'large' => ['width' => 1600, 'height' => 1600, 'quality' => 90],
        'original' => ['width' => null, 'height' => null, 'quality' => 95],
    ];

    /**
     * Optimize and store uploaded image
     * 
     * @return array URLs for different sizes
     */
    public function optimizeAndStore(UploadedFile $file, string $path): array
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        $urls = [];

        foreach ($this->sizes as $sizeName => $dimensions) {
            $filename = $this->generateFilename($file, $sizeName);
            $fullPath = "{$path}/{$filename}";

            // Resize if dimensions specified
            if ($dimensions['width'] && $dimensions['height']) {
                $optimized = $image->cover($dimensions['width'], $dimensions['height']);
            } else {
                $optimized = clone $image;
            }

            // Convert to WebP and set quality
            $optimized = $optimized->toWebp($dimensions['quality']);

            // Store
            Storage::disk('s3')->put($fullPath, $optimized->encode());

            // Generate URL
            $urls[$sizeName] = Storage::disk('s3')->url($fullPath);
        }

        // Generate blurhash placeholder
        $urls['blurhash'] = $this->generateBlurhash($image);

        return $urls;
    }

    /**
     * Optimize image from URL (for external images)
     */
    public function optimizeFromUrl(string $url, string $path): array
    {
        $response = Http::get($url);
        
        if (!$response->successful()) {
            throw new \Exception("Failed to download image from {$url}");
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($response->body());

        return $this->optimizeAndStoreFromResource($image, $path);
    }

    /**
     * Generate CDN-ready URLs with transformations
     * 
     * If using Imgix/Cloudinary, generate transformation URLs
     */
    public function getCdnUrl(string $originalUrl, array $transformations = []): string
    {
        // If using Imgix
        if (config('services.imgix.domain')) {
            $domain = config('services.imgix.domain');
            $path = parse_url($originalUrl, PHP_URL_PATH);
            
            $params = http_build_query(array_merge([
                'auto' => 'format,compress',
                'fit' => 'max',
            ], $transformations));
            
            return "https://{$domain}{$path}?{$params}";
        }

        // If using Cloudinary
        if (config('services.cloudinary.cloud_name')) {
            // Cloudinary URL transformation
            $transform = $this->buildCloudinaryTransform($transformations);
            return str_replace('/upload/', "/upload/{$transform}/", $originalUrl);
        }

        // Default: return original URL
        return $originalUrl;
    }

    /**
     * Generate responsive srcset attribute
     */
    public function getSrcset(array $urls): string
    {
        $widths = [
            'thumbnail' => 400,
            'medium' => 800,
            'large' => 1600,
        ];

        $srcset = [];
        foreach ($widths as $size => $width) {
            if (isset($urls[$size])) {
                $srcset[] = "{$urls[$size]} {$width}w";
            }
        }

        return implode(', ', $srcset);
    }

    /**
     * Generate blurhash placeholder
     */
    protected function generateBlurhash($image): string
    {
        // In production, use kornrunner/blurhash package
        // For now, return a simple color hash
        $color = $image->pickColor(1, 1, 'average');
        return $this->rgbToHex($color[0], $color[1], $color[2]);
    }

    /**
     * Generate filename with size suffix
     */
    protected function generateFilename(UploadedFile $file, string $size): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return "{$name}-{$size}.webp";
    }

    /**
     * Optimize from image resource
     */
    protected function optimizeAndStoreFromResource($image, string $path): array
    {
        $urls = [];

        foreach ($this->sizes as $sizeName => $dimensions) {
            $filename = "{$sizeName}-" . uniqid() . ".webp";
            $fullPath = "{$path}/{$filename}";

            if ($dimensions['width'] && $dimensions['height']) {
                $optimized = $image->cover($dimensions['width'], $dimensions['height']);
            } else {
                $optimized = clone $image;
            }

            $optimized = $optimized->toWebp($dimensions['quality']);
            Storage::disk('s3')->put($fullPath, $optimized->encode());
            $urls[$sizeName] = Storage::disk('s3')->url($fullPath);
        }

        $urls['blurhash'] = $this->generateBlurhash($image);

        return $urls;
    }

    /**
     * Build Cloudinary transformation string
     */
    protected function buildCloudinaryTransform(array $transformations): string
    {
        $parts = [];

        if (isset($transformations['width'])) {
            $parts[] = "w_{$transformations['width']}";
        }

        if (isset($transformations['height'])) {
            $parts[] = "h_{$transformations['height']}";
        }

        if (isset($transformations['quality'])) {
            $parts[] = "q_{$transformations['quality']}";
        }

        // Auto format and compress
        $parts[] = 'c_limit';
        $parts[] = 'f_auto';
        $parts[] = 'q_auto';

        return implode(',', $parts);
    }

    /**
     * Convert RGB to hex color
     */
    protected function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Clean up old images
     */
    public function cleanupOldImages(string $path, int $daysOld = 30): void
    {
        $files = Storage::disk('s3')->files($path);
        $cutoff = now()->subDays($daysOld);

        foreach ($files as $file) {
            $lastModified = Storage::disk('s3')->lastModified($file);
            
            if ($lastModified < $cutoff->timestamp) {
                Storage::disk('s3')->delete($file);
            }
        }
    }
}
