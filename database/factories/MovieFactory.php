<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3), // Random movie title
            'price' => $this->faker->randomFloat(2, 5, 50), // Price between 5 and 50
            'tag' => $this->faker->randomElement(['trending', 'under', null]), // Random tag
        ];
    }
}
