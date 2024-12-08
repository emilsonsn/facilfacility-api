<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $data = [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'user_id' => User::first()->id,
            'number' => $this->faker->randomNumber(5),
            'used' => $this->faker->word,
            'size' => $this->faker->randomNumber(2) . 'm2',
            'unity' => $this->faker->word,
            'report_last_update' => $this->faker->date,
            'consultant_name' => $this->faker->name,
            'city' => $this->faker->city,
            'region' => $this->faker->state,
            'country' => $this->faker->country,
            'zip_code' => $this->faker->postcode,
            'year_installed' => $this->faker->year,
            'replacement_cost' => $this->faker->randomFloat(2, 1000, 50000),
            'description' => $this->faker->text,
        ];

        return $data;        
    }
}
