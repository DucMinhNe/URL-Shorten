<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        // Cache mỗi setting (xoá tự động qua Setting model events). Tránh query mỗi click.
        $value = Cache::rememberForever(
            "setting:{$key}",
            fn () => Setting::where('key', $key)->first()?->getTypedValue()
        );

        return $value ?? $default;
    }

    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value,
             'type' => $type, 'updated_at' => now()]
        );
    }
}
