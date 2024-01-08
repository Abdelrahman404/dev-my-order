<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostsApiResource;
use App\Http\Resources\StoriesApiResource;
use App\Models\Follower;
use App\Models\Order;
use App\Models\Post;
use App\Models\Store;
use App\Models\Story;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Resources\SinglePostApiResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Zone;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index(){

        $userId = auth('api')->user()->id;
  
        $userFollowingListIds = Follower::where('follower_id', $userId)->pluck('following_id');

        $posts = Post::with(['comments.user', 'likes', 'order.details', 'user', 'store'])
                      ->whereIn('user_id', $userFollowingListIds)
                      ->latest()
                      ->take(50)
                      ->get();

        $stories = Story::with('user')->whereIn('user_id', $userFollowingListIds)
                        ->latest()
                        ->take(50)
                        ->get();
            
        $data['posts'] = PostsApiResource::collection($posts);

        $data['stories'] = StoriesApiResource::collection($stories);

        return response()->json($data);
    }

    public function discover(Request $request){

        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;
  
        $userFollowingListIds = Follower::where('follower_id', $userId)->pluck('following_id');
       
        $point = new Point($request->latitude, $request->longitude);
    
        $posts = Post::where('user_id', '!=', $userId)->whereNotIn('user_id', $userFollowingListIds)->whereHas('store.zone',  function($q) use ($point){
                          $q->contains('coordinates', $point);
                     })->with(['comments.user', 'likes', 'order.details', 'user', 'store'])
                    ->latest()
                    ->take(50)
                    ->get();

        $data['posts'] = PostsApiResource::collection($posts);

        return response()->json($data);
    }

    public function postDetails($id){

        $post = Post:: with(['comments.user', 'likes', 'order.details', 'user', 'store'])
                       ->findOrFail($id);

        $post->incrementViews();

        
        $data = new SinglePostApiResource($post);

        return response()->json($data);

    }

    public function likePost(Request $request){

        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;

        $post = Post::find($request->post_id);

        $post->incrementViews();

        Like::updateOrCreate([
            'user_id' => $userId,
            'post_id' => $request->post_id,
        ]);

        return response()->json(['message' => translate('messages.post_liked_successfully')], 200);

    }

    public function unlikePost(Request $request){

        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;

        $like = Like::where('post_id', $request->post_id)
                    ->where('user_id', $userId)->first();

        $like->delete();

        return response()->json(['message' => translate('messages.post_unliked_successfully')], 200);

    }

    public function addComment(Request $request){

        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string|min:1|max:999999'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;

        Comment::updateOrCreate([
                'user_id' => $userId,
                'post_id' => $request->post_id,
                'content' => $request->comment
        ]);

        return response()->json(['message' => translate('messages.comment_added_successfully')], 200);

    }

}
