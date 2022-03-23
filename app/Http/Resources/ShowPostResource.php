<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowPostResource extends JsonResource
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
            'username' => User::where('id', $this->user_id)->pluck('username'),
            'image_url' => User::where('id', $this->user_id)->pluck('image_url'),
            'image_full_url' => asset('storage/' . User::where('id', $this->user_id)->pluck('image_url')[0]),
            'post_id' => $this->id,
            'title' => $this->title,
            'category' => Category::where('id', $this->category_id)->pluck('name'),
            'category_slug' => Category::where('id', $this->category_id)->pluck('slug'),
            'created_at' => $this->created_at,
            'body' => $this->body
        ];
    }
}
