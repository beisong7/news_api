<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Http\Resources\NewsResource;
use App\Http\Requests\NewsSearchRequest;
use Illuminate\Http\Request;
use App\Services\NewsSearchService;
use App\Services\UserPreferenceService;

class NewsArticleController extends Controller
{
    protected $newsSearchService;
    protected $userPreferenceService;

    public function __construct(NewsSearchService $newsSearchService, UserPreferenceService $userPreferenceService)
    {
        $this->newsSearchService = $newsSearchService;
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * Get all news articles with optional filtering
     *
     * @param NewsSearchRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(NewsSearchRequest $request)
    {
        $articles = $this->newsSearchService->getArticles($request->validated());

        return NewsResource::collection($articles);
    }

    /**
     * Get a specific news article
     *
     * @param News $news
     * @return NewsResource
     */
    public function show(News $news)
    {
        return new NewsResource($news);
    }

    /**
     * Search news articles based on query
     *
     * @param NewsSearchRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(NewsSearchRequest $request)
    {
        $articles = $this->newsSearchService->searchArticles($request->validated());

        return NewsResource::collection($articles);
    }

    /**
     * Get news articles based on user preferences
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getRecommended(Request $request)
    {
        $user = $request->user('api');
        $articles = $this->userPreferenceService->getRecommendedArticles($user->id);

        return NewsResource::collection($articles);
    }
}
