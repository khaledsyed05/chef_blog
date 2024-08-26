<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Traits\LocaleTrait;
use App\Models\Recipe;
use App\Traits\LocaleTrait as TraitsLocaleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
use TraitsLocaleTrait;
    public function show(Request $request, $id)
    {
        $locale = $this->locale;
        if(!$recipe = Recipe::findOrFail($id)->published){
            return response()->json([
                'message' => 'Not found'
            ],404);
        }
        
        $recipe = Recipe::with([
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
        $userLike = $recipe->likes->where('user_id', Auth::id())->first();

        return response()->json([
            'recipe' => $recipe,
            'userLike' => $userLike ? $userLike->type : null,
            'likesCount' => $recipe->likesCount(),
            'dislikesCount'=> $recipe->dislikesCount()
        ]);
    }
}
