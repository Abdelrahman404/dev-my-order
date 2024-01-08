<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Highlight;
use Illuminate\Support\Facades\Validator;

class HightlightController extends Controller
{
    
    public function store(Request $request){

        $validator = Validator::make($request->all(), [

            'title' => 'required|string|min:3|max:50',
            'images' => 'required|array|max:12',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,gif|max:3000', // Maximum size in kilobytes (3 MB)
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $baseUrl = url('/');

        $imagesArray = [];

        foreach($request->file('images') as $image){

            $url = Helpers::upload('posts/', 'png', $image);

            $fullUrl = "$baseUrl/storage/highlights/$url";

            array_push($imagesArray, $fullUrl);
        }

        Highlight::updateOrCreate([
            
            'images' => json_encode($imagesArray),
            'user_id' => auth('api')->user()->id,
            'title' => $request->title
        ]);

        return response()->json(['message' => translate('messages.highlight_uploaded_successfully')], 200);
    }
}
