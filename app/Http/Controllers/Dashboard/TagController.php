<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Log::info('User: ', ['user' => auth()->user()]);
        // Log::info('Request: ', ['headers' => request()->headers->all()]);
        $tags = Tag::with('translations')->get();
        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'slug' => 'required|unique:tags,slug',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        $tag = Tag::create([
            'slug' => $validatedData['slug'],
        ]);

        foreach ($validatedData['name'] as $locale => $name) {
            $tag->translateOrNew($locale)->name = $name;
        }

        $tag->save();

        return response()->json([
            'message' => 'tag added successfully',
            'tag' => $tag->load('translations')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $validatedData = $request->validate([
            'slug' => [
                'sometimes',
                'required',
                Rule::unique('tags')->ignore($tag->id),
            ],
            'name' => 'sometimes|array',
            'name.*' => 'string|max:255',
        ]);
    
        // Only update slug if it's different from the current one
        if (isset($validatedData['slug']) && $validatedData['slug'] !== $tag->slug) {
            $tag->slug = $validatedData['slug'];
        }
    
        if (isset($validatedData['name'])) {
            foreach ($validatedData['name'] as $locale => $name) {
                $tag->translateOrNew($locale)->name = $name;
            }
        }
    
        $tag->save();
    
        return response()->json($tag->load('translations'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['message' => 'Tag trashed successfully']);
    }
    /**
     * Display a listing of trashed tags.
     */
    public function trashed()
    {
        $trashedTags = Tag::onlyTrashed()->with('translations')->get();
        return response()->json($trashedTags);
    }
    /**
     * Restore a trashed tag.
     */
    public function restore($id)
    {
        $tag = Tag::withTrashed()->findOrFail($id);
        $tag->restore();
        return response()->json(['message' => 'Tag restored successfully']);
    }
    /**
     * Force delete a tag.
     */
    public function forceDelete($id)
    {
        $tag = Tag::withTrashed()->findOrFail($id);
        $tag->forceDelete();
        return response()->json(['message' => 'Tag permanently deleted']);
    }
}
