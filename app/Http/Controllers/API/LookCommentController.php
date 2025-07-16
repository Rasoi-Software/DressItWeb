<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Look;
use App\Models\LookComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LookCommentController extends Controller
{
    public function toggleLike($lookId)
    {
        try {
            $user = Auth::user();
            $look = Look::findOrFail($lookId);

            if ($look->likes()->where('user_id', $user->id)->exists()) {
                $look->likes()->detach($user->id);
                return returnSuccess('Unliked successfully.');
            } else {
                $look->likes()->attach($user->id);
                return returnSuccess('Liked successfully.');
            }
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function store(Request $request, $lookId)
    {
        
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        try {
            $look = Look::findOrFail($lookId);

            $comment = $look->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $request->comment,
            ]);

            return returnSuccess('Comment added.', $comment);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function index($lookId)
    {
        try {
           $comments = LookComment::with('user')->where('look_id',$lookId)->get();
            return returnSuccess('Comments fetched successfully.', $comments);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $comment = LookComment::with('user')->findOrFail($id);
            return returnSuccess('Comment fetched successfully.', $comment);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        try {
            $comment = LookComment::findOrFail($id);

            if ($comment->user_id !== Auth::id()) {
                return returnError('Unauthorized', 403);
            }

            $comment->update(['comment' => $request->comment]);
            return returnSuccess('Comment updated successfully.', $comment);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $comment = LookComment::findOrFail($id);

            if ($comment->user_id !== Auth::id()) {
                return returnError('Unauthorized', 403);
            }

            $comment->delete();
            return returnSuccess('Comment deleted successfully.');
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

 
}
