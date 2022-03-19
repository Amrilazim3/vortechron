<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class EditPostResource extends JsonResource
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
            'title' => $this->title,
            'thumbnail' => $this->thumbnail,
            'excerpt' => $this->excerpt,            
            'category' => Category::where('id', $this->category_id)->pluck('name'),
            'body' => $this->body
        ];
    }
}
