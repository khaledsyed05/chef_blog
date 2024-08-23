<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function getHomePageData(Request $request)
    {
        $locale = $request->header('Accept-Language', App::getLocale());
        App::setLocale($locale);

        $search = $request->query('search');

        if ($search) {
            // Only perform search if a query is provided
            $searchResults = [
                'recipes' => Recipe::with(['translations' => function($query) use ($locale) {
                        $query->where('locale', $locale);
                    }, 'category.translations' => function($query) use ($locale) {
                        $query->where('locale', $locale);
                    }, 'tags.translations' => function($query) use ($locale) {
                        $query->where('locale', $locale);
                    }, 'user'])
                    ->where('published', true)
                    ->where(function ($query) use ($search) {
                        $query->whereHas('translations', function ($q) use ($search) {
                            $q->where('title', 'LIKE', "%{$search}%")
                                ->orWhere('description', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('tags.translations', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('category.translations', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                    })
                    ->take(5)->with('likes')
                    ->get(),
                'articles' => Article::with(['translations' => function($query) use ($locale) {
                        $query->where('locale', $locale);
                    }, 'tags.translations' => function($query) use ($locale) {
                        $query->where('locale', $locale);
                    }, 'user'])
                    ->where('published', true)
                    ->where(function ($query) use ($search) {
                        $query->whereHas('translations', function ($q) use ($search) {
                            $q->where('title', 'LIKE', "%{$search}%")
                                ->orWhere('content', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('tags.translations', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                    })
                    ->take(5)
                    ->get()
            ];

            return response()->json([
                'searchResults' => $searchResults,
            ]);
        } else {
            // If no search query, return regular home page data
            $featuredRecipes = Recipe::with(['translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'category.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'tags.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'user'])
                ->where('published', true)
                ->where('featured', true)
                ->take(5)
                ->get();

            $latestRecipes = Recipe::with(['translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'category.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'tags.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'user'])
                ->where('published', true)
                ->latest()
                ->take(10)
                ->get();

            $latestArticles = Article::with(['translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'tags.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }, 'user'])
                ->where('published', true)
                ->latest()
                ->take(5)
                ->get();

            return response()->json([
                'featuredRecipes' => $featuredRecipes,
                'latestRecipes' => $latestRecipes,
                'latestArticles' => $latestArticles,
            ]);
        }
    }
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $search = $request->query('query');

        $recipes = Recipe::with('translations', 'category', 'tags', 'user')
            ->where('published', true)
            ->search($search)
            ->take(5)
            ->get();

        $articles = Article::with('translations', 'tags', 'user')
            ->where('published', true)
            ->search($search)
            ->take(5)
            ->get();

        return response()->json([
            'recipes' => $recipes,
            'articles' => $articles,
        ]);
    }
}
