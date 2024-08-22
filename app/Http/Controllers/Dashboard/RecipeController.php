<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;
use App\Http\Requests\SeoRequest;
use App\Http\Requests\NutritionFactRequest;
use App\Models\Recipe;
use App\Models\NutritionFact;
use App\Models\Seo;
use App\Traits\HandlesSeo;
use App\Traits\SavesNutritionFact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    use HandlesSeo, SavesNutritionFact;
    public function index()
    {
        $recipes = Recipe::with(['translations', 'category', 'tags', 'user', 'seo'])->get();
        return response()->json($recipes);
    }

    public function store(RecipeRequest $request, SeoRequest $seoRequest, NutritionFactRequest $nutritionRequest)
    {
        $validatedData = $request->validated();

        $recipe = new Recipe();
        $recipe->user_id = auth()->id();
        $recipe->category_id = $validatedData['category_id'];
        $recipe->youtube_video = $validatedData['youtube_video'] ?? null;
        $recipe->featured = $validatedData['featured'] ?? false;
        $recipe->published = $validatedData['published'] ?? false;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('recipes', 'public');
            $recipe->cover_image = $path;
        }

        $recipe->save();

        // Save translations
        $translatableFields = ['title', 'description', 'ingredients', 'instructions', 'total_time'];
        foreach ($translatableFields as $field) {
            foreach ($validatedData[$field] as $locale => $value) {
                if ($field === 'ingredients' || $field === 'instructions') {
                    // Ensure we're working with an array
                    $arrayValue = is_string($value) ? json_decode($value, true) : $value;

                    // Store as a JSON string without additional escaping
                    $recipe->translateOrNew($locale)->{$field} = json_encode($arrayValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    $recipe->translateOrNew($locale)->{$field} = $value;
                }
            }
        }

        $recipe->save();
        $this->validateAndSaveSeo($recipe, $seoRequest);
        $this->saveNutritionFact($recipe, $nutritionRequest);



        // Attach tags
        if (isset($validatedData['tags']) && is_array($validatedData['tags'])) {
            $recipe->tags()->attach($validatedData['tags']);
        }

        return response()->json([
            'message' => 'Recipe created successfully',
            'recipe' => $recipe->load(['translations', 'category', 'tags', 'user', 'seo', 'nutrition'])
        ], 201);
    }

    public function show(Recipe $recipe)
    {
        return response()->json($recipe->load(['translations', 'category', 'tags', 'user', 'seo'. 'nutrition']));
    }

    public function update(RecipeRequest $request, SeoRequest $seoRequest, Recipe $recipe, NutritionFactRequest $nutritionRequest)
    {
        $validatedData = $request->validated();

        // Handle cover_image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover_image
            if ($recipe->cover_image) {
                Storage::disk('public')->delete($recipe->cover_image);
            }
            $path = $request->file('cover_image')->store('recipes', 'public');
            $recipe->cover_image = $path;
        }

        $recipe->category_id = $validatedData['category_id'] ?? $recipe->category_id;
        $recipe->youtube_video = $validatedData['youtube_video'] ?? $recipe->youtube_video;
        $recipe->featured = $validatedData['featured'] ?? $recipe->featured;
        $recipe->published = $validatedData['published'] ?? $recipe->published;

        // Update translations
        $translatableFields = ['title', 'description', 'ingredients', 'instructions', 'total_time'];
        foreach ($translatableFields as $field) {
            foreach ($validatedData[$field] as $locale => $value) {
                if ($field === 'ingredients' || $field === 'instructions') {
                    // Ensure we're working with an array
                    $arrayValue = is_string($value) ? json_decode($value, true) : $value;

                    // Store as a JSON string without additional escaping
                    $recipe->translateOrNew($locale)->{$field} = json_encode($arrayValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    $recipe->translateOrNew($locale)->{$field} = $value;
                }
            }
        }

        // Update tags
        if (isset($validatedData['tags'])) {
            $recipe->tags()->sync($validatedData['tags']);
        }

        $recipe->save();
        $this->validateAndSaveSeo($recipe, $seoRequest);
        $this->saveNutritionFact($recipe, $nutritionRequest);


        return response()->json([
            'message' => 'Recipe updated successfully',
            'recipe' => $recipe->load(['translations', 'category', 'tags', 'user', 'seo','nutrition'])
        ]);
    }
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return response()->json(['message' => 'Recipe trashed successfully']);
    }

    public function trashed()
    {
        $trashedRecipes = Recipe::onlyTrashed()->with(['translations', 'category', 'tags', 'user'])->get();
        return response()->json($trashedRecipes);
    }

    public function restore($id)
    {
        $recipe = Recipe::withTrashed()->findOrFail($id);
        $recipe->restore();
        return response()->json(['message' => 'Recipe restored successfully']);
    }

    public function forceDelete($id)
    {
        $recipe = Recipe::withTrashed()->findOrFail($id);

        // Delete the cover_image file
        if ($recipe->cover_image) {
            Storage::disk('public')->delete($recipe->cover_image);
        }

        $recipe->forceDelete();
        return response()->json(['message' => 'Recipe permanently deleted']);
    }
    public function toggleFeature(Recipe $recipe)
    {
        $published = $recipe->published;
        $featured = $recipe->toggleFeature();
        if ($published === false) {
            return response()->json([
                'message' => 'Cannot feature an unpublished recipe',
                'featured' => false
            ], 400);
        }
        return response()->json(['featured' => $featured]);
    }

    public function togglePublished(Recipe $recipe)
    {
        $published = $recipe->togglePublished();
        return response()->json([
            'published' => $published,
            'featured' => $recipe->featured // This might have changed if unpublished
        ]);
    }

}
