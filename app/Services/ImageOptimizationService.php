<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * T-1603: Image Optimization Pipeline
 *
 * Processes uploaded auction images into multiple WebP size variants,
 * generates a blurhash colour placeholder, stores results to S3 (or
 * the configured default disk), and optionally serves via Imgix CDN.
 *
 * Returns an array with keys: thumbnail, medium, large, original, blurhash, width, height.
 */
class ImageOptimizationService
{
    /** @var array<string, array{width: int|null, height: int|null, quality: int}> */
    private const SIZES = [
        'thumbnail' => ['width' => 400,  'height' => 400,  'quality' => 80],
        'medium'    => ['width' => 800,  'height' => 800,  'quality' => 85],
        'large'     => ['width' => 1600, 'height' => 1600, 'quality' => 90],
        'original'  => ['width' => null, 'height' => null, 'quality' => 95],
    ];

    /**
     * Optimise an uploaded file and store all size variants.
     *
     * @return array{thumbnail: string, medium: string, large: string, original: string, blurhash: string, width: int, height: int}
     */
    public function optimizeAndStore(UploadedFile $file, string $folder = 'auction-images'): array
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->read($file->getRealPath());

        $originalWidth  = $image->width();
        $originalHeight = $image->height();

        $disk = $this->disk();
        $uuid = Str::uuid()->toString();
        $urls = [];

        foreach (self::SIZES as $sizeName => $dims) {
            $path = "{$folder}/{$uuid}-{$sizeName}.webp";

            if ($dims['width'] !== null && $dims['height'] !== null) {
                // Cover crop: fills the target box, no upscaling beyond original
                $resized = $manager->read($file->getRealPath())
                    ->scaleDown(max: $dims['width'])
                    ->toWebp($dims['quality']);
            } else {
                $resized = $manager->read($file->getRealPath())
                    ->toWebp($dims['quality']);
            }

            Storage::disk($disk)->put($path, $resized->toFilePointer());

            $urls[$sizeName] = $this->resolveUrl($disk, $path);
        }

        $urls['blurhash'] = $this->generatePlaceholder($file->getRealPath());
        $urls['width']    = $originalWidth;
        $urls['height']   = $originalHeight;

        return $urls;
    }

    /**
     * Return the URL for an image path, applying Imgix CDN transformations if configured.
     *
     * @param array<string, mixed> $transformations  Imgix query params (e.g. ['w' => 800, 'q' => 85])
     */
    public function getCdnUrl(string $originalUrl, array $transformations = []): string
    {
        $imgixDomain = config('services.imgix.domain');

        if ($imgixDomain) {
            $urlPath = parse_url($originalUrl, PHP_URL_PATH);

            $params = http_build_query(array_merge([
                'auto' => 'format,compress',
                'fit'  => 'max',
            ], $transformations));

            return "https://{$imgixDomain}{$urlPath}?{$params}";
        }

        return $originalUrl;
    }

    /**
     * Build a responsive srcset string from the stored optimized_urls array.
     *
     * @param array<string, string> $urls  Keys: thumbnail, medium, large
     */
    public function getSrcset(array $urls): string
    {
        $widths = ['thumbnail' => 400, 'medium' => 800, 'large' => 1600];
        $parts  = [];

        foreach ($widths as $size => $width) {
            if (! empty($urls[$size])) {
                $parts[] = "{$urls[$size]} {$width}w";
            }
        }

        return implode(', ', $parts);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function disk(): string
    {
        // Prefer S3; fall back to the application default (public on local).
        $configured = config('filesystems.default', 'public');

        return $configured === 's3' ? 's3' : $configured;
    }

    private function resolveUrl(string $disk, string $path): string
    {
        if ($disk === 'public') {
            return '/storage/'.$path;
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Generate a compact colour placeholder from the image's dominant hue.
     *
     * For a real blurhash string install kornrunner/blurhash and replace this
     * implementation.  The hex-colour approach is sufficient as a low-cost
     * placeholder that can be used as a CSS background while the real image loads.
     */
    private function generatePlaceholder(string $realPath): string
    {
        try {
            $manager = new ImageManager(new Driver());
            // Sample a small version for performance
            $thumb = $manager->read($realPath)->scale(width: 8);
            $color = $thumb->pickColor(4, 4);

            return sprintf('#%02x%02x%02x', $color->red(), $color->green(), $color->blue());
        } catch (\Throwable) {
            return '#cccccc';
        }
    }
}
