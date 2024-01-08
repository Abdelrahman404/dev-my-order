<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class SinglePostApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'images' => json_decode($this->images),
            'user' => new UserApiResource($this->whenLoaded('user')),
            'views' => $this->views,
            'store' => new StoreApiResource($this->whenLoaded('store')),
            'comments' => CommentsApiResource::collection($this->whenLoaded('comments')),
            'likes' =>$this->formattedLikes(),
            'likes_count' => $this->whenLoaded('likes')->count(),
            'liked_by_current_user' => $this->is_liked_by_current_user,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }

    protected function formattedLikes()
    {
        $likesCount = $this->whenLoaded('likes')->count();

        // Check if there are any likes
        if ($likesCount > 0) {
            // Get the names of the first likers (Jane Doe, John Smith, ...)
            $firstLikers = $this->likes->take(2)->pluck('user.f_name')->implode(', ');

            // Check if there are more likers
            $othersCount = $likesCount - 2;
            $othersText = $othersCount > 0 ? " and $othersCount others" : '';

            return "$firstLikers$othersText";
        }

        return '';
    }
}
