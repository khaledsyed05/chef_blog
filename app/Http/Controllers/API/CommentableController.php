<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Recipe;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentableController extends Controller
{
    public function index(Request $request, $type, $id)
    {
        $model = $this->getModel($type);
        $item = $model::findOrFail($id);
        $comments = $item->comments()->with('user')->get();
        if ($comments->isEmpty()) {
            return response()->json(['message' => "There are no comments yet"]);
        }
        return response()->json(['comments' => $comments]);
    }

    public function store(Request $request, $type, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $model = $this->getModel($type);
        $item = $model::findOrFail($id);

        $comment = $item->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json(['comment' => $comment->load('user')], 201);
    }

    public function update(Request $request, $type, $id, Comment $comment)
    {
        $this->authorize('update', $comment);

        $model = $this->getModel($type);
        $item = $model::findOrFail($id);

        // Check if the comment belongs to the correct commentable item
        if ($comment->commentable_id != $item->id || $comment->commentable_type != get_class($item)) {
            return response()->json(['message' => 'Comment does not belong to this ' . $type], 400);
        }

        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validatedData);

        return response()->json($comment);
    }

    public function destroy($type, $id, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $model = $this->getModel($type);
        $item = $model::findOrFail($id);

        // Check if the comment belongs to the correct commentable item
        if ($comment->commentable_id != $item->id || $comment->commentable_type != get_class($item)) {
            return response()->json(['message' => 'Comment does not belong to this ' . $type], 400);
        }

        $comment->delete();

        return response()->json([
            "message" => "comment deleted successfully"
        ]);
    }

    private function getModel($type)
    {
        switch ($type) {
            case 'recipe':
                return Recipe::class;
            case 'article':
                return Article::class;
            default:
                abort(404, 'Invalid type');
        }
    }
}
