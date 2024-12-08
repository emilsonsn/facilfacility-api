<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Component;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Action>
 */
class ActionFactory extends Factory
{
    protected $model = Action::class;

    public function definition()
    {
        return [
            'component_id' => Component::factory(),
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement(['maintenance', 'repair', 'inspection']),
            'date' => $this->faker->date(),
            'category' => $this->faker->name,
            'condition' => $this->faker->randomElement(['good', 'fair', 'poor']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'frequency' => $this->faker->name,
            'coast' => $this->faker->randomFloat(2, 100, 1000),
            'curracy' => 'USD',
            'description' => $this->faker->sentence,
            'image' => null, // Deixe como null por padrão
        ];
    }
}
