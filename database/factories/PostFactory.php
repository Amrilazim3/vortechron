<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
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
            'user_id' => $userId[$k],
            'category_id' => rand(1, 43),
            'slug' => $this->faker->unique()->slug(),
            'title' => $this->faker->sentence(2),
            'thumbnail' => null,
            'excerpt' => $this->faker->sentence(12),
            'body' => '<p>' . implode('</p></p>', $this->faker->paragraphs(12)) . '</p>',
        ];
    }
}
