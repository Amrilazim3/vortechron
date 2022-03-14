<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPostResource extends JsonResource
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
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'created_at' => $this->created_at
        ];
    }
}
