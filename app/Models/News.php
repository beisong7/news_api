<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'external_id',
        'title',
        'content',
        'summary',
        'url',
        'image_url',
        'author',
        'published_at',
        'category_id',
        'unique_hash'
    ];

    /**
     * Get the category that the article belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * The source of the news article
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    // Static method to generate a unique hash for deduplication
    public static function generateUniqueHash(string $title, ?string $content = null)
    {
        // Use enough content to ensure uniqueness but not the entire content
        $contentSnippet = $content ? substr($content, 0, 100) : '';
        return md5($title . $contentSnippet);
    }
}
