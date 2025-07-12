<?php

namespace Database\Factories;

use Carbon\Carbon;
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
        $startDate = Carbon::create(2025, 7, 1, 0, 0, 0);
$endDate = Carbon::now(); // Hoy

$randomDate = Carbon::createFromTimestamp(mt_rand($startDate->timestamp, $endDate->timestamp));

        return [
            'name'=> $this->faker->title,
            'status'=> 'Pendiente',
            'date'=> $randomDate,
            'description'=> $this->faker->sentence,
            'price'=> $this->faker->randomFloat(2, 10, 100),
            'weight'=> $this->faker->randomFloat(2, 1, 10),
            'customer_id'=> $this->faker->numberBetween(1,6),
        ];
    }
}
