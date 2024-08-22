<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Article;
use App\Models\Like;
class LikeableController extends Controller
{
    public function toggleLike(Request $request, $type, $id)
    {
        $user = auth()->user();
        $isLike = $request->input('is_like');

        $model = $this->getModel($type, $id);

        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }
        $like = Like::updateOrCreate(
            [
                'user_id' => $user->id,
                'likeable_id' => $model->id,
                'likeable_type' => get_class($model)
            ],
            ['is_like' => $isLike]
        );  

        return response()->json([
            'message' => $isLike ? 'Liked successfully' : 'Disliked successfully',
            'likes_count' => $model->likesCount(),
            'dislikes_count' => $model->dislikesCount(),
        ]);
    }

    public function unlike($type, $id)
    {
        $user = auth()->user();
        $model = $this->getModel($type, $id);

        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $like = Like::where('user_id', $user->id)
                    ->where('likeable_id', $model->id)
                    ->where('likeable_type', get_class($model))
                    ->first();

        if ($like) {
            $like->delete();
            $message = 'Like/dislike removed successfully';
        } else {
            $message = 'No like/dislike found';
        }

        return response()->json([
            'message' => $message,
            'likes_count' => $model->likesCount(),
            'dislikes_count' => $model->dislikesCount(),
        ]);
    }

    private function getModel($type, $id)
    {
        switch ($type) {
            case 'recipe':
                return Recipe::find($id);
            case 'article':
                return Article::find($id);
            default:
                return null;
        }
    }
}
