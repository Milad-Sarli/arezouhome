<?php

namespace App\Models\Concerns;

use App\Services\ImageVariants;
use Illuminate\Database\Eloquent\Model;

/**
 * Auto-generate responsive WebP variants whenever an image field changes.
 *
 * Usage in model:
 *   use GeneratesImageVariants;
 *   public function variantImageFields(): array { return ['image']; }
 */
trait GeneratesImageVariants
{
    public static function bootGeneratesImageVariants(): void
    {
        static::saved(function (Model $model): void {
            if (app()->runningUnitTests()) {
                return;
            }

            $fields = method_exists($model, 'variantImageFields') ? $model->variantImageFields() : [];

            foreach ($fields as $field) {
                if (! $model->wasChanged($field)) {
                    continue;
                }

                $path = $model->getAttribute($field);

                if (! is_string($path) || $path === '') {
                    continue;
                }

                try {
                    foreach (ImageVariants::WIDTHS as $width) {
                        ImageVariants::ensure($path, $width);
                    }
                } catch (\Throwable) {
                    // Never break admin saves because of image processing.
                }
            }
        });
    }
}
