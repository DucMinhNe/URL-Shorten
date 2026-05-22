<?php

use App\Models\Setting;
use App\Services\SettingService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns default when key missing', function () {
    $svc = new SettingService();
    expect($svc->get('missing_key', 'fallback'))->toBe('fallback');
});

it('returns typed integer value', function () {
    Setting::create(['key' => 'rate', 'value' => '5000', 'type' => 'integer']);
    $svc = new SettingService();
    expect($svc->get('rate'))->toBe(5000);
});

it('sets and persists value', function () {
    $svc = new SettingService();
    $svc->set('foo', 'bar');
    expect(Setting::where('key','foo')->value('value'))->toBe('bar');
});
