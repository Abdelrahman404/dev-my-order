<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\Story;

class StoryController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:3000', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $baseUrl = url('/');

        $url = Helpers::upload('posts/', 'png', $request->file('image'));

        $fullUrl = "$baseUrl/storage/stories/$url";

        Story::updateOrCreate([
            'image' => $fullUrl,
            'user_id' => auth('api')->user()->id,
        ]);

        return response()->json(['message' => translate('messages.story_uploaded_successfully')], 200);
    }
}
