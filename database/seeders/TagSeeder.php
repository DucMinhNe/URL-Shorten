<?php

namespace Database\Seeders;

use App\Models\ShortLink;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Khuyến mãi',  'color' => 'rose',    'icon' => 'heroicon-o-gift',          'is_featured' => true],
            ['name' => 'Sale 9.9',    'color' => 'red',     'icon' => 'heroicon-o-fire',          'is_featured' => true],
            ['name' => 'Học tập',     'color' => 'blue',    'icon' => 'heroicon-o-academic-cap',  'is_featured' => true],
            ['name' => 'Video',       'color' => 'pink',    'icon' => 'heroicon-o-video-camera',  'is_featured' => true],
            ['name' => 'Tin tức',     'color' => 'cyan',    'icon' => 'heroicon-o-newspaper'],
            ['name' => 'Tài liệu',    'color' => 'amber',   'icon' => 'heroicon-o-document-text'],
            ['name' => 'Game',        'color' => 'violet',  'icon' => 'heroicon-o-puzzle-piece'],
            ['name' => 'Shopee',      'color' => 'orange',  'icon' => 'heroicon-o-shopping-bag'],
            ['name' => 'Tiki',        'color' => 'blue',    'icon' => 'heroicon-o-book-open'],
            ['name' => 'Lazada',      'color' => 'indigo',  'icon' => 'heroicon-o-shopping-cart'],
            ['name' => 'Bài tập',     'color' => 'green',   'icon' => 'heroicon-o-pencil'],
            ['name' => 'Affiliate',   'color' => 'amber',   'icon' => 'heroicon-o-link',          'is_featured' => true],
            ['name' => 'TikTok',      'color' => 'rose',    'icon' => 'heroicon-o-musical-note'],
            ['name' => 'YouTube',     'color' => 'red',     'icon' => 'heroicon-o-play'],
            ['name' => 'Facebook',    'color' => 'blue',    'icon' => 'heroicon-o-user-group'],
            ['name' => 'Zalo',        'color' => 'cyan',    'icon' => 'heroicon-o-chat-bubble-left'],
            ['name' => 'Truyện',      'color' => 'violet',  'icon' => 'heroicon-o-book-open'],
            ['name' => 'Phim',        'color' => 'rose',    'icon' => 'heroicon-o-film'],
            ['name' => 'Crypto',      'color' => 'amber',   'icon' => 'heroicon-o-currency-dollar'],
            ['name' => 'Tech',        'color' => 'slate',   'icon' => 'heroicon-o-cpu-chip'],
            ['name' => 'Phong cách',  'color' => 'pink',    'icon' => 'heroicon-o-sparkles'],
            ['name' => 'Du lịch',     'color' => 'cyan',    'icon' => 'heroicon-o-globe-asia-australia'],
            ['name' => 'Ăn uống',     'color' => 'orange',  'icon' => 'heroicon-o-cake'],
            ['name' => 'Beauty',      'color' => 'pink',    'icon' => 'heroicon-o-sparkles'],
            ['name' => 'Sức khoẻ',    'color' => 'green',   'icon' => 'heroicon-o-heart'],
            ['name' => 'Đầu tư',      'color' => 'emerald', 'icon' => 'heroicon-o-arrow-trending-up'],
            ['name' => 'Việc làm',    'color' => 'indigo',  'icon' => 'heroicon-o-briefcase'],
            ['name' => 'Bất động sản','color' => 'amber',   'icon' => 'heroicon-o-home'],
            ['name' => 'Auto',        'color' => 'slate',   'icon' => 'heroicon-o-truck'],
            ['name' => 'Music',       'color' => 'violet',  'icon' => 'heroicon-o-musical-note'],
        ];

        $created = [];
        foreach ($tags as $t) {
            $tag = Tag::create([
                'name' => $t['name'],
                'slug' => Str::slug($t['name']),
                'color' => $t['color'],
                'icon' => $t['icon'] ?? null,
                'is_featured' => $t['is_featured'] ?? false,
                'description' => 'Tag '.$t['name'],
            ]);
            $created[] = $tag->id;
        }

        // Attach tags to ~25% of links
        $linkIds = ShortLink::inRandomOrder()->limit(2500)->pluck('id');
        $pivot = [];
        foreach ($linkIds as $lid) {
            $count = fake()->numberBetween(1, 3);
            $picked = collect($created)->random($count);
            foreach ($picked as $tid) {
                $pivot[] = ['short_link_id' => $lid, 'tag_id' => $tid];
            }
            if (count($pivot) >= 500) {
                DB::table('short_link_tag')->insertOrIgnore($pivot);
                $pivot = [];
            }
        }
        if ($pivot) {
            DB::table('short_link_tag')->insertOrIgnore($pivot);
        }

        // Update usage_count
        DB::statement('UPDATE tags t SET usage_count = (SELECT COUNT(*) FROM short_link_tag WHERE tag_id = t.id)');
    }
}
