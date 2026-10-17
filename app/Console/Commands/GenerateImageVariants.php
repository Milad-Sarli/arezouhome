<?php

namespace App\Console\Commands;

use App\Services\ImageVariants;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('app:generate-image-variants')]
#[Description('Pre-generate responsive WebP variants (320/640/960/1280) for storage images')]
class GenerateImageVariants extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = Storage::disk('public');
        $count = 0;

        foreach (['products', 'sliders', 'categories', 'blogs'] as $dir) {
            if (! $disk->exists($dir)) {
                continue;
            }

            foreach ($disk->allFiles($dir) as $file) {
                if (str_starts_with($file, 'cache/') || ! ImageVariants::isSupported($file)) {
                    continue;
                }

                foreach (ImageVariants::WIDTHS as $w) {
                    if (ImageVariants::ensure($file, $w)) {
                        $count++;
                    }
                }
            }
        }

        $this->info("Generated {$count} variants.");

        return self::SUCCESS;
    }
}
