<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdCampaignFactory extends Factory
{
    public function definition(): array
    {
        $placement = fake()->randomElement(['top', 'side', 'bottom']);
        $size = match ($placement) {
            'top', 'bottom' => '728/90',
            'side' => '300/250',
        };

        return [
            'name' => fake()->company().' '.strtoupper($placement),
            'placement' => $placement,
            'type' => 'banner_image',
            'content' => "https://picsum.photos/seed/{$placement}".fake()->numberBetween(1, 1000)."/{$size}",
            'target_url' => fake()->url(),
            'weight' => fake()->numberBetween(1, 10),
            'status' => 'active',
        ];
    }
}
