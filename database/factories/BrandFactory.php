<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{

    public function definition(): array
    {
        return [
            'title' => $this->faker->company(),
            'slug' => $this->faker->company(),
            // TODO 3rd lesson
            'thumbnail' => ''
        ];
    }
}
