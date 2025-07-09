<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'=> $this->faker->title,
            'status'=> 'Pendientes',
            'date'=> $this-> faker->dateTimeBetween('-1 year', 'now'),
            'description'=> $this->faker->sentence,
            'price'=> $this->faker->randomFloat(2, 10, 100),
            'weight'=> $this->faker->randomFloat(2, 1, 10),
            'customer_id'=> $this->faker->numberBetween(1,6),
        ];
    }
}
