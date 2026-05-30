<?php

namespace Database\Seeders;

use App\Models\CaptchaQuestion;
use Illuminate\Database\Seeder;

class CaptchaQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['3 + 4 bằng mấy?', ['5', '6', '7', '8'], '7'],
            ['Con vật nào kêu "gâu gâu"?', ['Mèo', 'Chó', 'Gà', 'Vịt'], 'Chó'],
            ['Thủ đô của Việt Nam là?', ['TP.HCM', 'Đà Nẵng', 'Hà Nội', 'Huế'], 'Hà Nội'],
            ['Lá cây thường có màu gì?', ['Đỏ', 'Xanh lá', 'Tím', 'Vàng'], 'Xanh lá'],
            ['Số nào lớn nhất?', ['12', '8', '21', '15'], '21'],
            ['Một tuần có mấy ngày?', ['5', '6', '7', '10'], '7'],
            ['2 × 5 bằng mấy?', ['7', '10', '12', '25'], '10'],
            ['Mặt trời mọc ở hướng nào?', ['Tây', 'Bắc', 'Đông', 'Nam'], 'Đông'],
        ];

        foreach ($questions as [$q, $opts, $answer]) {
            CaptchaQuestion::firstOrCreate(
                ['question' => $q],
                [
                    'options' => array_map(fn ($o) => ['text' => $o, 'correct' => $o === $answer], $opts),
                    'is_active' => true,
                ],
            );
        }
    }
}
