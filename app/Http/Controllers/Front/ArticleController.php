<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Traits\LocaleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    use LocaleTrait;
    public function show(Request $request, $id)
    {
        $locale = $this->locale;
        if(!$article = Article::findOrFail($id)->published){
            return response()->json([
                'message' => 'Not found'
            ],404);
        }
        
        $article = Article::with([
            'translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'category.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'tags.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'user',
            'comments.user',

        ])->findOrFail($id);
        $userLike = $article->likes->where('user_id', Auth::id())->first();

        return response()->json([
            'article' => $article,
            'userLike' => $userLike ? $userLike->type : null,
            'likesCount' => $article->likesCount(),
            'dislikesCount'=> $article->dislikesCount()
        ]);
    }

}
