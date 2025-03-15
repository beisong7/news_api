<?php

namespace App\Services;

use App\Models\News;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserPreferenceService
{
    /**
     * Get recommended articles based on user preferences
     *
     * @param int $userId
     * @return LengthAwarePaginator
     */
    public function getRecommendedArticles(int $userId): LengthAwarePaginator
    {
        // Get user with relationships

        $user = User::with(['preferredCategories', 'excludedCategories'])->find($userId);

        // dd($user->toArray());

        // If user not found, return latest news
        if (!$user) {
            return News::latest('published_at')->paginate(15);
        }

        $query = News::query();

        // Apply preference filters
        $this->applyPreferenceFilters($query, $user);

        return $query->latest('published_at')->paginate(15);
    }

    /**
     * Apply user preference filters to the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return void
     */
    protected function applyPreferenceFilters($query, User $user): void
    {
        // Get preferred and excluded category IDs
        $preferredCategoryIds = $user->preferredCategories->pluck('id')->toArray();
        $excludedCategoryIds = $user->excludedCategories->pluck('id')->toArray();

        // Include preferred categories if any are specified
        if (!empty($preferredCategoryIds)) {
            $query->whereIn('category_id', $preferredCategoryIds);
        }

        // Always exclude excluded categories
        if (!empty($excludedCategoryIds)) {
            $query->whereNotIn('category_id', $excludedCategoryIds);
        }

        // Add more preference-based filters as needed
        // For example, preferred or excluded sources
    }

    /**
     * Save user category preferences
     *
     * @param int $userId
     * @param array $preferredCategoryIds
     * @param array $excludedCategoryIds
     * @return bool
     */
    public function saveUserCategoryPreferences(int $userId, array $preferredCategoryIds = [], array $excludedCategoryIds = []): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // Start a transaction to ensure data integrity
        \DB::beginTransaction();

        try {
            // Remove all existing relationships first to avoid conflicts
            $user->categories()->detach();

            // Attach preferred categories
            foreach ($preferredCategoryIds as $categoryId) {
                $user->categories()->attach($categoryId, ['preference_type' => 'preferred']);
            }

            // Attach excluded categories
            foreach ($excludedCategoryIds as $categoryId) {
                $user->categories()->attach($categoryId, ['preference_type' => 'excluded']);
            }

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return false;
        }
    }
}
