<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Traits\LocaleTrait as TraitsLocaleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    use TraitsLocaleTrait;


    public function search(Request $request)
    {
        $search = $request->query('search');
        $locale = $this->locale;

        if (empty($search)) {
            return response()->json([
                'searchResults' => [
                    'recipes' => [],
                    'articles' => []
                ],
                'message' => 'No search term provided'
            ]);
        }

        $searchResults = [
            'recipes' => Recipe::with(['translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }, 'category.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }, 'tags.translations' => function ($query) use ($locale) {
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
            'articles' => Article::with(['translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }, 'tags.translations' => function ($query) use ($locale) {
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
    }
    public function featuredRecipes()
    {
        $locale = $this->locale;

        $featuredRecipes = Recipe::with(['translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'category.translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'tags.translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'user'])
            ->where('published', true)
            ->where('featured', true)
            ->take(5)
            ->get();

        return response()->json([
            'featuredRecipes' => $featuredRecipes,
        ]);
    }
    public function publishedRecipes(Request $request)
    {
        $locale = $this->locale;
        $latestRecipes = Recipe::with(['translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'category.translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'tags.translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'user'])
            ->where('published', true)
            ->latest()
            ->take(10)
            ->get();


        return response()->json([
            'latestRecipes' => $latestRecipes,
        ]);
    }
    public function publishedArticles()
    {
        $locale = $this->locale;
        $Articles = Article::with(['translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'tags.translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }, 'user'])
            ->where('published', true)
            ->latest()
            ->take(5)
            ->get();
        return response()->json([
            'Articles' => $Articles,
        ]);
    }
}
