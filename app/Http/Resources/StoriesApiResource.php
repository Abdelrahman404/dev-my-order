<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class StoriesApiResource extends JsonResource
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
            'image' => $this->image,
            'user' => new UserApiResource($this->whenLoaded('user')),
            'created_at' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }

}
