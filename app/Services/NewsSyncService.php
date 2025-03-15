<?php

namespace App\Services;

use App\Models\News;
use App\Models\Source;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsSyncService
{
    private $categoryService;

    public function __construct(CategoryService $cs){
        $this->categoryService = $cs;
    }
    /**
     * Sync news from all active sources
     */
    public function syncAllSources(): void
    {
        $sources = Source::where('active', true)->get();


        foreach ($sources as $source) {
            try {
                $this->syncSource($source);
            } catch (\Exception $e) {
                Log::error("Error syncing source {$source->name}: " . $e->getMessage());
            }
        }
    }

    /**
     * Sync news from a specific source
     */
    public function syncSource(Source $source): void
    {
        $data = $this->fetchData($source);

        if (!$data) {
            Log::warning("No data returned from source: {$source->name}");
            return;
        }

        $articles = $this->extractArticles($source, $data);

        foreach ($articles as $article) {
            $this->processArticle($source, $article);
        }
    }

    /**
     * Fetch data from the source API
     */
    protected function fetchData(Source $source)
    {
        // Prepare HTTP client
        $http = Http::timeout(30);

        // Add authorization header if source uses headers
        if ($source->uses_header && $source->api_key) {
            $http = $http->withHeaders([
                'Authorization' => "Bearer {$source->api_key}",
            ]);
        }

        // Parse default params from JSON string
        $params = [];
        if ($source->default_params) {
            $params = json_decode($source->default_params, true) ?? [];

            // Handle special case for date parameter
            if (isset($params['date'])) {
                // $params['date'] = now()->format('Y-m-d');
                $params['date'] = now()->subHours(8)->format('Y-m-d');
            }
        }

        // Add API key as a query parameter if not using headers
        if (!$source->uses_header && $source->api_key && $source->api_key_param) {
            $params[$source->api_key_param] = $source->api_key;
        }

        // Make the request based on the method
        $response = null;
        if (strtolower($source->method) === 'post') {
            $response = $http->post($source->base_url, $params);
        } else {
            $response = $http->get($source->base_url, $params);
        }

        Log::info("calling {$source->base_url} with paams ".json_encode($params));

        if ($response->successful()) {
            return $response->json();
        }else{
            // dd($params, $response);
        }

        Log::error("Failed to fetch data from {$source->name}: {$response->status()} - {$response->body()}");
        return null;
    }

    /**
     * Extract articles based on source type
     */
    protected function extractArticles(Source $source, array $data): array
    {

        //debug
        // if($source->type==="world_news_api"){
        //     dd($data);
        // }
        // Apply different extraction logic based on source type
        switch ($source->type) {
            case 'news_api':
                return $data['articles'] ?? [];

            case 'guardian':
                return $data['response']['results'] ?? [];

            case 'world_news_api':
                $articles = [];
                foreach ($data['top_news'] ?? [] as $group) {
                    if (isset($group['news']) && is_array($group['news'])) {
                        foreach($group['news'] as $news){
                            $articles = array_merge($articles, $group['news']);
                        }

                    }
                }
                return $articles;

            default:
                // For unknown sources, attempt to find arrays in the response
                // that might contain articles
                foreach ($data as $key => $value) {
                    if (is_array($value) && !empty($value)) {
                        if (isset($value[0]) && is_array($value[0])) {
                            return $value;
                        }
                    }
                }
                return [];
        }
    }

    /**
     * Process a single article
     */
    protected function processArticle(Source $source, array $articleData): void
    {
        // Map the fields according to our mappings
        $mappedData = $this->mapArticleFields($source, $articleData);

        if (empty($mappedData['title'])) {
            Log::warning("Article from {$source->name} has no title, skipping");
            return;
        }

        // Generate unique hash for deduplication
        $uniqueHash = News::generateUniqueHash(
            $mappedData['title'],
            $mappedData['content'] ?? null
        );

        // Check if we already have this article
        $existingNews = News::where('source_id', $source->id)
            ->where('unique_hash', $uniqueHash)
            ->first();

        if ($existingNews) {
            // Article already exists, skip
            return;
        }

        $categoryId = $this->categoryService->getOrCreateCategory($mappedData['category'] ?? null);

        // Remove category from mappedData since we'll use category_id instead
        unset($mappedData['category']);

        // Create new article
        News::create(array_merge($mappedData, [
            'source_id' => $source->id,
            'unique_hash' => $uniqueHash,
            'category_id' => $categoryId,
        ]));
    }

    /**
     * Map article fields according to source field mappings
     */
    protected function mapArticleFields(Source $source, array $articleData): array
    {
        $mappings = $source->fieldMappings()->get()->keyBy('source_field');
        $result = [];

        foreach ($mappings as $sourceField => $mapping) {
            $value = $this->extractValueByPath($articleData, $sourceField, $mapping->path);

            if ($value !== null) {
                $targetField = $mapping->target_field;

                // Special handling for published_at to ensure it's a valid date
                if ($targetField === 'published_at' && is_string($value)) {
                    try {
                        $value = Carbon::parse($value);
                    } catch (\Exception $e) {
                        $value = now();
                    }
                }

                $result[$targetField] = $value;
            }
        }

        return $result;
    }

    /**
     * Extract a value from nested arrays using dot notation path
     */
    protected function extractValueByPath(array $data, string $field, ?string $path = null)
    {
        // If no path is specified, check if the field exists directly
        if (empty($path)) {
            return $data[$field] ?? null;
        }

        // Otherwise, navigate through the path
        $segments = explode('.', $path);
        $current = $data;

        foreach ($segments as $segment) {
            if (!isset($current[$segment])) {
                return null;
            }
            $current = $current[$segment];
        }

        return $current;
    }
}
