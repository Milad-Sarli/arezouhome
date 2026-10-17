<?php

namespace App\Http\Controllers;

use App\Services\ImageVariants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageVariantController extends Controller
{
    /**
     * Serve (and lazily generate) a responsive WebP variant.
     *
     * GET /img/{width}/{path} e.g. /img/640/products/digi-01.webp
     */
    public function show(Request $request, int $width, string $path): mixed
    {
        $width = ImageVariants::nearest($width);
        $path = ltrim($path, '/');

        if (str_contains($path, '..') || ! ImageVariants::isSupported($path)) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        $resolved = ImageVariants::ensure($path, $width) ?? $path;

        $absolute = $disk->path($resolved);
        $mime = str_ends_with($absolute, '.webp') ? 'image/webp' : mime_content_type($absolute);

        return response()->file($absolute, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
