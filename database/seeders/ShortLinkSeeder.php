<?php

namespace Database\Seeders;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            'https://shopee.vn', 'https://tiki.vn', 'https://lazada.vn',
            'https://tinhte.vn', 'https://vnexpress.net', 'https://thanhnien.vn',
        ];

        $target = 10000;
        $seen = [];
        $passwordHash = Hash::make('demo123');
        $batch = [];

        for ($i = 0; $i < $target; $i++) {
            $useCustom = fake()->boolean(30);
            $hasPwd = fake()->boolean(10);

            do {
                $slug = $useCustom
                    ? Str::slug(fake()->words(2, true)).fake()->numberBetween(1, 99999)
                    : Str::random(7);
            } while (isset($seen[$slug]));
            $seen[$slug] = true;

            $createdAt = fake()->dateTimeBetween('-90 days', 'now')->format('Y-m-d H:i:s');
            $batch[] = [
                'user_id' => fake()->boolean(85) ? fake()->randomElement($users) : null,
                'slug' => $slug,
                'original_url' => fake()->boolean(40) ? fake()->randomElement($realUrls) : fake()->url(),
                'password' => $hasPwd ? $passwordHash : null,
                'status' => fake()->randomElement(['active', 'active', 'active', 'disabled']),
                'total_clicks' => 0,
                'valid_views' => 0,
                'total_earned' => 0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if (count($batch) >= 500) {
                DB::table('short_links')->insert($batch);
                $batch = [];
            }
        }
        if ($batch) {
            DB::table('short_links')->insert($batch);
        }
    }
}
