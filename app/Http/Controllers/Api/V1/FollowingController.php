<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Follower;


class FollowingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        Follower::updateOrCreate([
            'follower_id' => auth()->user()->id,
            'following_id' => $request->user_id
        ]);

        return response()->json(['message' => translate('messages.successfully_followed')], 200);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // Find the follower relationship
        $follower = Follower::where('follower_id', auth()->user()->id)
            ->where('following_id', $request->user_id)
            ->first();

        if (!$follower) {
            return response()->json(['message' => translate('messages.user_is_not_being_followed')], 200);
        }

        $follower->delete();

        return response()->json(['message' => translate('messages.successfully_unfollowed')], 200);

    }
}
