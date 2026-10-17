<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * On-demand responsive image variants (WebP) for storage images.
 *
 * Source: storage/app/public/{path} (e.g. products/digi-01.webp)
 * Cached variant: storage/app/public/cache/{width}/{path}
 * Served publicly as: {APP_URL}/storage/cache/{width}/{path}
 */
class ImageVariants
{
    /** @var int[] */
    public const WIDTHS = [320, 640, 960, 1280];

    public const QUALITY = 82;

    public static function isSupported(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['webp', 'jpg', 'jpeg', 'png'], true);
    }

    public static function cachePath(string $path, int $width): string
    {
        return "cache/{$width}/{$path}";
    }

    public static function url(string $path, int $width): ?string
    {
        if (! self::isSupported($path)) {
            return null;
        }

        if (! in_array($width, self::WIDTHS, true)) {
            $width = self::nearest($width);
        }

        return asset('storage/'.self::cachePath($path, $width));
    }

    /** @return array<int, array{width: int, url: string}> */
    public static function urls(string $path): array
    {
        $out = [];

        foreach (self::WIDTHS as $w) {
            $url = self::url($path, $w);

            if ($url) {
                $out[] = ['width' => $w, 'url' => $url];
            }
        }

        return $out;
    }

    public static function nearest(int $width): int
    {
        $best = self::WIDTHS[0];
        $bestDiff = abs($width - $best);

        foreach (self::WIDTHS as $w) {
            $diff = abs($width - $w);

            if ($diff < $bestDiff) {
                $best = $w;
                $bestDiff = $diff;
            }
        }

        return $best;
    }

    /**
     * Ensure a cached variant exists; returns its storage-relative path or null.
     */
    public static function ensure(string $path, int $width): ?string
    {
        $path = ltrim($path, '/');

        if (str_contains($path, '..') || ! self::isSupported($path)) {
            return null;
        }

        if (! in_array($width, self::WIDTHS, true)) {
            $width = self::nearest($width);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $cached = self::cachePath($path, $width);

        if ($disk->exists($cached) && $disk->lastModified($cached) >= $disk->lastModified($path)) {
            return $cached;
        }

        $src = $disk->path($path);
        $info = @getimagesize($src);

        if (! $info) {
            return null;
        }

        [$srcW, $srcH] = $info;

        if ($srcW <= $width) {
            return $path;
        }

        $ratio = $width / $srcW;
        $dstW = $width;
        $dstH = max(1, (int) round($srcH * $ratio));

        $srcImg = self::createFrom($src, strtolower(pathinfo($src, PATHINFO_EXTENSION)));

        if (! $srcImg) {
            return null;
        }

        $dstImg = imagecreatetruecolor($dstW, $dstH);

        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagedestroy($srcImg);

        $tmp = tempnam(sys_get_temp_dir(), 'imgv').'.webp';
        imagewebp($dstImg, $tmp, self::QUALITY);
        imagedestroy($dstImg);

        $disk->put($cached, file_get_contents($tmp));
        @unlink($tmp);

        return $cached;
    }

    private static function createFrom(string $file, string $ext): mixed
    {
        return match ($ext) {
            'webp' => @imagecreatefromwebp($file),
            'jpg', 'jpeg' => @imagecreatefromjpeg($file),
            'png' => @imagecreatefrompng($file),
            default => null,
        } ?: null;
    }
}
