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
        return [
            'user_id' => User::factory(),
            'slug' => $this->faker->unique()->slug(),
            'title' => $this->faker->sentence(),
            'thumbnail' => $this->faker->imageUrl(),
            'excerpt' => '<p>' . implode('</p></p>', $this->faker->paragraphs(2)) . '</p>',
            'body' => '<p>' . implode('</p></p>', $this->faker->paragraphs(6)) . '</p>',
        ];
    }
}
