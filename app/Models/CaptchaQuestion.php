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

    /** Chỉ số (index trong options) của các ô đúng. */
    public function correctIndices(): array
    {
        return collect($this->options ?? [])
            ->map(fn ($o, $i) => ! empty($o['correct']) ? $i : null)
            ->filter(fn ($v) => $v !== null)
            ->values()->all();
    }

    /** Người dùng chọn đúng = tập ô chọn trùng khớp hoàn toàn tập ô đúng. */
    public function isSolved(string|array|null $selected): bool
    {
        $sel = collect(is_array($selected) ? $selected : explode(',', (string) $selected))
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->map(fn ($v) => (int) $v)->unique()->sort()->values()->all();

        $correct = collect($this->correctIndices())->sort()->values()->all();

        return count($correct) > 0 && $sel === $correct;
    }

    /** Lấy ngẫu nhiên 1 câu hỏi đang bật (hoặc null nếu chưa cấu hình). */
    public static function pickActive(): ?self
    {
        return static::where('is_active', true)->inRandomOrder()->first();
    }
}
