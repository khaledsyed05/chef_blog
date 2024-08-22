<?php

namespace App\Http\Controllers\Dashboard;

use App\Traits\HandlesSeo;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\SeoRequest;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use HandlesSeo;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with(['translations', 'user', 'tags'])->get();
        return response()->json($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, SeoRequest $seoRequest)
    {
        $validatedData = $request->validate([
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'content' => 'required|array',
            'content.*' => 'required|string',
            'cover_image' => 'nullable|image|max:2048', // 2MB Max
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $article = new Article();
        $article->user_id = auth()->id();
        $article->slug = Str::slug($validatedData['title']['en']); // Assuming English is the default language

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('articles', 'public');
            $article->cover_image = $path;
        }

        $article->save();
        $this->validateAndSaveSeo($article, $seoRequest);


        foreach (['title', 'content'] as $field) {
            foreach ($validatedData[$field] as $locale => $value) {
                $article->translateOrNew($locale)->{$field} = $value;
            }
        }

        $article->save();
        $this->validateAndSaveSeo($article, $seoRequest);


        if (isset($validatedData['tags'])) {
            $article->tags()->attach($validatedData['tags']);
        }

        return response()->json([
            'message' => 'Article created successfully',
            'article' => $article->load(['translations', 'user', 'tags'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return response()->json($article->load(['translations', 'user', 'tags']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article, SeoRequest $seoRequest)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|array',
            'title.*' => 'string|max:255',
            'content' => 'sometimes|array',
            'content.*' => 'string',
            'cover_image' => 'nullable|image|max:2048', // 2MB Max
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if (isset($validatedData['title']['en'])) {
            $article->slug = str::slug($validatedData['title']['en']);
        }

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('articles', 'public');
            $article->cover_image = $path;
        }

        foreach (['title', 'content'] as $field) {
            if (isset($validatedData[$field])) {
                foreach ($validatedData[$field] as $locale => $value) {
                    $article->translateOrNew($locale)->{$field} = $value;
                }
            }
        }

        $article->save();
        $this->validateAndSaveSeo($article, $seoRequest);


        if (isset($validatedData['tags'])) {
            $article->tags()->sync($validatedData['tags']);
        }

        return response()->json($article->load(['translations', 'user', 'tags', 'seo']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(['message' => 'Article trashed successfully']);
    }

    /**
     * Display a listing of trashed articles.
     */
    public function trashed()
    {
        $trashedArticles = Article::onlyTrashed()->with(['translations', 'user', 'tags'])->get();
        return response()->json($trashedArticles);
    }

    /**
     * Restore a trashed article.
     */
    public function restore($id)
    {
        $article = Article::withTrashed()->findOrFail($id);
        $article->restore();
        return response()->json(['message' => 'Article restored successfully']);
    }

    /**
     * Force delete an article.
     */
    public function toggleFeature(Article $article)
    {
        $published = $article->published;
        $featured = $article->toggleFeature();
        if ($published === false) {
            return response()->json([
                'message' => 'Cannot feature an unpublished article',
                'featured' => false
            ], 400);
        }
        return response()->json(['featured' => $featured]);
    }

    public function togglePublished(Article $article)
    {
        $published = $article->togglePublished();
        return response()->json([
            'published' => $published,
            'featured' => $article->featured // This might have changed if unpublished
        ]);
    }
}
