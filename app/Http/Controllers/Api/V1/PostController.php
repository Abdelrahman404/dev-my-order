<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;


class PostController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'required|array',
            'images.*' => 'required|file|mimes:jpeg,png,jpg,gif|max:3000', // Maximum size in kilobytes (3 MB)
            'order_id' => 'required|exists:orders,id', // Assuming orders table exists
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::find($request->order_id);
        
        $baseUrl = url('/');

        $imagesArray = [];

        foreach($request->file('images') as $image){

            $url = Helpers::upload('posts/', 'png', $image);

            $fullUrl = "$baseUrl/storage/posts/$url";

            array_push($imagesArray, $fullUrl);
        }

        Post::updateOrCreate([
            'images' => json_encode($imagesArray),
            'user_id' => auth('api')->user()->id,
            'store_id' => $order->store_id,
            'order_id' => $request->order_id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['message' => translate('messages.post_added_successfully')], 200);
    }
    
}
