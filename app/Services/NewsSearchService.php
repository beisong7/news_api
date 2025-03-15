<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsSearchService
{
    /**
     * Get articles with optional filtering
     *
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getArticles(array $params): LengthAwarePaginator
    {
        $query = News::query();

        $this->applyFilters($query, $params);

        return $query->latest('published_at')
            ->paginate($params['per_page'] ?? 15);
    }

    /**
     * Search articles based on search query and filters
     *
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function searchArticles(array $params): LengthAwarePaginator
    {
        $query = News::query();

        // Apply search query
        if (!empty($params['search'])) {
            $searchTerm = $params['search'];
            $query->where(function (Builder $query) use ($searchTerm) {
                $query->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('summary', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('author', 'LIKE', "%{$searchTerm}%");
            });
        }

        $this->applyFilters($query, $params);

        return $query->latest('published_at')
            ->paginate($params['per_page'] ?? 15);
    }

    /**
     * Apply filters to the query
     *
     * @param Builder $query
     * @param array $params
     * @return void
     */
    protected function applyFilters(Builder $query, array $params): void
    {
        // Filter by source
        if (!empty($params['source_id'])) {
            $query->where('source_id', $params['source_id']);
        }

        // Filter by category (updated to use category_id)
        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        // Filter by author
        if (!empty($params['author'])) {
            $query->where('author', $params['author']);
        }

        // Filter by date range
        if (!empty($params['date_from'])) {
            $query->whereDate('published_at', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->whereDate('published_at', '<=', $params['date_to']);
        }

        // Sort options
        $sortField = $params['sort_by'] ?? 'published_at';
        $sortDirection = $params['sort_direction'] ?? 'desc';

        $allowedSortFields = ['published_at', 'title', 'author'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }
    }
}
