<?php

namespace Database\Factories;

use App\Models\Component;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Component>
 */
class ComponentFactory extends Factory
{
    protected $model = Component::class;

    public function definition()
    {
        return [
            'facility_id' => \App\Models\Facility::factory(), // Cria uma Facility associada
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'quantity' => $this->faker->randomNumber(2),
            'unity' => $this->faker->name,
            'coast' => $this->faker->randomFloat(2, 100, 1000),
            'currency' => 'USD',
            'time_left_by_condition' => $this->faker->randomNumber(1),
            'time_left_by_lifespan' => $this->faker->randomNumber(1),
            'condition' => $this->faker->randomElement(['new', 'used']),
            'year_installed' => $this->faker->year,
        ];
    }
}
