<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;
use App\Http\Resources\WatchlistApiResource;
use App\Http\Resources\WatchlistCollectionApiResource;
use App\Models\Item;
use App\Models\WatchListCollection;
use App\Models\WatchListCollectionProduct;
use MercadoPago\Config\Json;

class WatchlistController extends Controller
{
    public function index(){

        $userId = auth('api')->user()->id;

        $watchlist = Watchlist::with('item')->where('user_id', $userId)->latest()->get();

        $watchlist_collection= WatchListCollection::where('user_id', $userId)->latest()->get();

        $data['watchlist'] = WatchlistApiResource::collection($watchlist);

        $data['watchlist_collection'] = WatchlistCollectionApiResource::collection($watchlist_collection);

        return response()->json($data);

    }


    public function getCollectionItems(Request $request){

        $validator = Validator::make($request->all(), [
            'collection_id'=>'required|exists:watch_list_collections,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;

        $itemIds = WatchListCollectionProduct::where('watch_list_collection_id', $request->collection_id)
                                             ->pluck('product_id');

        $items = Item::whereIn('id', $itemIds)->get();

        return response()->json($items);

    }

    public function addItem(Request $request){

        $validator = Validator::make($request->all(), [
            'item_id'=>'required|exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;

        Watchlist::updateOrCreate([
            'user_id' => $userId,
            'item_id' => $request->item_id
        ]);

        return response()->json(['message'=>translate('messages.item_added_to_watchlist_successfully')],200);
    }

    public function deleteItem(Request $request){

        $validator = Validator::make($request->all(), [
            'item_id'=>'required|exists:watchlists,item_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;

        Watchlist::where('user_id', $userId)->where('item_id', $request->item_id)->delete();

        return response()->json(['message'=>translate('messages.item_removed_from_watchlist_successfully')],200);
    
    }

    public function createCollection(Request $request){
        
  
        $validator = Validator::make($request->all(), [
            'name'=>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user()->id;

        WatchListCollection::updateOrCreate([
            'user_id' => $userId,
            'name' => $request->name
        ]);

        return response()->json(['message'=>translate('messages.collection_created_successfully')],200);
    } 

    
    public function addItemToCollection(Request $request){
        
        $validator = Validator::make($request->all(), [
            'item_id'=>'required|exists:items,id',
            'collection_id'=>'required|exists:watch_list_collections,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        WatchListCollectionProduct::updateOrCreate([
            'watch_list_collection_id' => $request->collection_id,
            'product_id' => $request->item_id
        ]);

        return response()->json(['message'=>translate('messages.item_added_to_watchlist_successfully')],200);

    }

    public function removeItemFromCollection(Request $request){

        $validator = Validator::make($request->all(), [
            'item_id'=>'required|exists:items,id',
            'collection_id'=>'required|exists:watch_list_collections,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

       WatchListCollectionProduct::where('product_id', $request->item_id)
                                ->where('watch_list_collection_id', $request->collection_id)
                                ->delete();

      return response()->json(['message'=>translate('messages.item_removed_from_watchlist_successfully')],200);

    }
}
