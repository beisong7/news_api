<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get or create a category by name
     *
     * @param string|null $categoryName
     * @return int The category ID
     */
    public function getOrCreateCategory(?string $categoryName): int
    {
        // If no category name is provided, use "Others"
        $categoryName = $categoryName ?: 'Others';

        // Try to find the category
        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            ['slug' => \Str::slug($categoryName)]
        );

        return $category->id;
    }
}
