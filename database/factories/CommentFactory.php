<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userId = [41,45,72,73,76,77];
        $k = array_rand($userId);

        return [
            'post_id' => 180,
            'user_id' => $userId[$k],
            'body' => $this->faker->sentence(2)
        ];
    }
}
