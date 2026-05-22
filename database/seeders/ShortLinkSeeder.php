<?php

namespace Database\Seeders;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShortLinkSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->pluck('id')->toArray();
        $realUrls = [
            'https://github.com/laravel/laravel', 'https://laravel.com/docs',
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'https://filamentphp.com/docs',
            'https://php.net/manual/en/index.php', 'https://tailwindcss.com',
        ];

        for ($i = 0; $i < 300; $i++) {
            $useCustom = fake()->boolean(30);
            $hasPwd = fake()->boolean(10);

            do {
                $slug = $useCustom ? Str::slug(fake()->words(2, true)).fake()->numberBetween(1, 9999) : Str::random(6);
            } while (ShortLink::where('slug', $slug)->exists());

            ShortLink::create([
                'user_id' => fake()->boolean(85) ? fake()->randomElement($users) : null,
                'slug' => $slug,
                'original_url' => fake()->boolean(40) ? fake()->randomElement($realUrls) : fake()->url(),
                'password' => $hasPwd ? Hash::make('demo123') : null,
                'status' => fake()->randomElement(['active', 'active', 'active', 'disabled']),
                'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
                'updated_at' => now(),
            ]);
        }
    }
}
