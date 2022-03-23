<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCommentResource extends JsonResource
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
            'username' => User::where('id', $this->user_id)->pluck('username')[0],
            'image_url' => User::where('id', $this->user_id)->pluck('image_url')[0],
            'image_full_url' => asset('storage/' . User::where('id', $this->user_id)->pluck('image_url')[0]),
            'user_id' => $this->user_id,
            'body' => $this->body,
            'created_at' => $this->created_at
        ];
    }
}
