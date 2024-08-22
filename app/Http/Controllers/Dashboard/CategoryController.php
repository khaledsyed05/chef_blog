<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('translations')->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'slug' => 'required|unique:categories,slug',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
        ]);

        $category = Category::create([
            'slug' => $validatedData['slug'],
        ]);

        foreach ($validatedData['name'] as $locale => $name) {
            $category->translateOrNew($locale)->name = $name;
        }

        if (isset($validatedData['description'])) {
            foreach ($validatedData['description'] as $locale => $description) {
                $category->translateOrNew($locale)->description = $description;
            }
        }

        $category->save();

        return response()->json([
            'message' => 'Category added successfully',
            'category' => $category->load('translations')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category->load('translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'slug' => [
                'sometimes',
                'required',
                Rule::unique('categories')->ignore($category->id),
            ],
            'name' => 'sometimes|array',
            'name.*' => 'string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
        ]);

        if (isset($validatedData['slug']) && $validatedData['slug'] !== $category->slug) {
            $category->slug = $validatedData['slug'];
        }

        if (isset($validatedData['name'])) {
            foreach ($validatedData['name'] as $locale => $name) {
                $category->translateOrNew($locale)->name = $name;
            }
        }

        if (isset($validatedData['description'])) {
            foreach ($validatedData['description'] as $locale => $description) {
                $category->translateOrNew($locale)->description = $description;
            }
        }

        $category->save();

        return response()->json($category->load('translations'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category trashed successfully']);
    }

    /**
     * Display a listing of trashed categories.
     */
    public function trashed()
    {
        $trashedCategories = Category::onlyTrashed()->with('translations')->get();
        return response()->json($trashedCategories);
    }

    /**
     * Restore a trashed category.
     */
    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();
        return response()->json(['message' => 'Category restored successfully']);
    }

    /**
     * Force delete a category.
     */
    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();
        return response()->json(['message' => 'Category permanently deleted']);
    }
}
