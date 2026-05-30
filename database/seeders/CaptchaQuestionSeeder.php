<?php

namespace Database\Seeders;

use App\Models\CaptchaQuestion;
use Illuminate\Database\Seeder;

class CaptchaQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset bộ mặc định rồi nạp lại các thử thách lưới 9 ô.
        CaptchaQuestion::query()->delete();

        // mỗi ô: [nội dung, đúng?]
        $cell = fn ($items) => array_map(fn ($x) => ['text' => $x[0], 'correct' => (bool) $x[1]], $items);

        $challenges = [
            ['Chọn tất cả ô có con chó 🐶', [
                ['🐶', 1], ['🐱', 0], ['🦊', 0],
                ['🐶', 1], ['🐭', 0], ['🐶', 1],
                ['🐰', 0], ['🐯', 0], ['🐶', 1],
            ]],
            ['Chọn tất cả ô có xe cộ 🚗', [
                ['🚗', 1], ['🍎', 0], ['🐶', 0],
                ['🚲', 1], ['🌳', 0], ['🚗', 1],
                ['🐱', 0], ['✈️', 1], ['🍌', 0],
            ]],
            ['Chọn các ô có số chẵn', [
                ['1', 0], ['2', 1], ['3', 0],
                ['4', 1], ['5', 0], ['6', 1],
                ['7', 0], ['8', 1], ['9', 0],
            ]],
            ['Chọn các ô có số: 2, 3, 4, 5', [
                ['1', 0], ['2', 1], ['3', 1],
                ['4', 1], ['5', 1], ['6', 0],
                ['7', 0], ['8', 0], ['9', 0],
            ]],
            ['Chọn tất cả ô có trái cây 🍎', [
                ['🍎', 1], ['🐶', 0], ['🍌', 1],
                ['🚗', 0], ['🍇', 1], ['🐱', 0],
                ['🍊', 1], ['🌳', 0], ['🍉', 1],
            ]],
        ];

        foreach ($challenges as [$prompt, $cells]) {
            CaptchaQuestion::create([
                'question' => $prompt,
                'options' => $cell($cells),
                'is_active' => true,
            ]);
        }
    }
}
