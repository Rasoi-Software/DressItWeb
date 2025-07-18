<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LookCommentsReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LookCommentsReplyController extends Controller
{
    public function index()
    {
        try {
            $replies = LookCommentsReply::with('comment')->get();
            return returnSuccess('Replies fetched.', $replies);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'look_comment_id' => 'required|exists:look_comments,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        try {
            $reply = LookCommentsReply::create($request->only(['look_comment_id', 'content']));
            return returnSuccess('Reply added.', $reply);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $reply = LookCommentsReply::with('comment')->findOrFail($id);
            return returnSuccess('Reply fetched.', $reply);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        try {
            $reply = LookCommentsReply::findOrFail($id);
            $reply->update($request->only(['content']));
            return returnSuccess('Reply updated.', $reply);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $reply = LookCommentsReply::findOrFail($id);
            $reply->delete();
            return returnSuccess('Reply deleted.');
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }
}
