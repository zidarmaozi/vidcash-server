<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VideoFactory extends Factory
{
    public function definition(): array
    {
        $videoId = Str::random(10);
        return [
            'title' => 'Video ' . $this->faker->words(3, true),
            'video_code' => $videoId,
            'original_link' => 'https://videy.co/v/?id=' . $videoId,
            'generated_link' => 'https://videy.in/v/?id=' . $videoId,
            'validation_level' => $this->faker->numberBetween(1, 10),
        ];
    }
}