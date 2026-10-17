<?php

namespace App\Models;

use App\Models\Concerns\GeneratesImageVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use GeneratesImageVariants;
    use SoftDeletes;

    protected $fillable = [
        'title', 'meta_title', 'meta_description', 'canonical_url', 'excerpt',
        'slug', 'blog_category_id', 'image', 'description',
        'keywords', 'publish_at', 'status', 'is_featured', 'views',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'publish_at' => 'datetime',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'views' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    protected static function booted(): void
    {
        static::saving(function (self $blog) {
            if ($blog->isDirty('description') && $blog->description) {
                $blog->description = strip_tags($blog->description, '<p><br><strong><em><ul><ol><li><h2><h3><a><span><blockquote><table><thead><tbody><tr><th><td>');
                // Remove javascript: from href
                $blog->description = preg_replace('/href\s*=\s*["\']\s*javascript:/i', 'href="#"', $blog->description);
            }

            if (($blog->isDirty('description') || $blog->isDirty('excerpt')) && empty($blog->excerpt) && $blog->description) {
                $text = trim(preg_replace('/\s+/', ' ', strip_tags($blog->description)));
                $blog->excerpt = mb_substr($text, 0, 160);
            }
        });
    }

    /** @return string[] */
    public function variantImageFields(): array
    {
        return ['image'];
    }
}
