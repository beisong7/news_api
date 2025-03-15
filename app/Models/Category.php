<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the news articles that belong to the category
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * Get the users that have this category as a preference
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('preference_type')->withTimestamps();
    }
}
