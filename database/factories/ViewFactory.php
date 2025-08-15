<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ViewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ip_address' => $this->faker->ipv4(),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}