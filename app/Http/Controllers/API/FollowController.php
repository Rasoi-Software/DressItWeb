<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FollowController extends Controller
{
    public function follow(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'following_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $user = Auth::user();

        if ($user->id == $request->following_id) {
            return returnError('You cannot follow yourself');
        }

        $followedUser = User::findOrFail($request->following_id);

        $user->following()->syncWithoutDetaching([$followedUser->id]);

        return returnSuccess('User followed successfully');
    }

    public function unfollow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'following_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $user = Auth::user();

        if ($user->id == $request->following_id) {
            return returnError('You cannot unfollow yourself');
        }

        $unfollowUser = User::findOrFail($request->following_id);

        $user->following()->detach($unfollowUser->id);

        return returnSuccess('User unfollowed successfully');
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        return returnSuccess('Followers list fetched', $user->followers);
    }

    public function following($id)
    {
        $user = User::findOrFail($id);
        return returnSuccess('Following list fetched', $user->following);
    }
}
