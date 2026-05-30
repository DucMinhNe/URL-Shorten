<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaptchaQuestion extends Model
{
    protected $fillable = ['question', 'image', 'options', 'is_active', 'shown_count'];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'shown_count' => 'integer',
    ];

    /** Danh sách text đáp án đúng. */
    public function correctAnswers(): array
    {
        return collect($this->options ?? [])
            ->filter(fn ($o) => ! empty($o['correct']))
            ->pluck('text')->all();
    }

    public function isCorrect(?string $answer): bool
    {
        return $answer !== null && in_array($answer, $this->correctAnswers(), true);
    }

    /** Lấy ngẫu nhiên 1 câu hỏi đang bật (hoặc null nếu chưa cấu hình). */
    public static function pickActive(): ?self
    {
        return static::where('is_active', true)->inRandomOrder()->first();
    }
}
