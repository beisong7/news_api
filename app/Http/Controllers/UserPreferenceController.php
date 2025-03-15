<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class UserPreferenceController extends Controller
{
    protected $userPreferenceService;

    public function __construct(UserPreferenceService $userPreferenceService)
    {
        $this->userPreferenceService = $userPreferenceService;
        $this->middleware('auth:api');
    }

    /**
     * Get user's category preferences
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPreferences()
    {
        $user = auth()->user();
        $preferredCategories = $user->preferredCategories;
        $excludedCategories = $user->excludedCategories;

        return response()->json([
            'preferred_categories' => CategoryResource::collection($preferredCategories),
            'excluded_categories' => CategoryResource::collection($excludedCategories),
        ]);
    }

    /**
     * Update user's category preferences
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'integer|exists:categories,id',
            'excluded_categories' => 'nullable|array',
            'excluded_categories.*' => 'integer|exists:categories,id',
        ]);

        $preferredCategories = $validated['preferred_categories'] ?? [];
        $excludedCategories = $validated['excluded_categories'] ?? [];

        // Check for conflicts (categories can't be both preferred and excluded)
        $conflicts = array_intersect($preferredCategories, $excludedCategories);
        if (!empty($conflicts)) {
            return response()->json([
                'message' => 'Categories cannot be both preferred and excluded',
                'conflicts' => $conflicts
            ], 422);
        }

        $result = $this->userPreferenceService->saveUserCategoryPreferences(
            auth()->id(),
            $preferredCategories,
            $excludedCategories
        );

        if ($result) {
            return response()->json([
                'message' => 'Preferences updated successfully'
            ]);
        }

        return response()->json([
            'message' => 'Failed to update preferences'
        ], 500);
    }

    /**
     * Get all available categories
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getCategories()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }
}
