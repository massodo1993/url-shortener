<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'original_url' => fake()->url(),
            'clicks_count' => 0,
        ];
    }
}
