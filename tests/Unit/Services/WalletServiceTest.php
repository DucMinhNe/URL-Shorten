<?php

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('credits user balance atomically and records transaction', function () {
    $user = User::factory()->create(['balance' => 1000, 'total_earned' => 0]);
    $svc = new WalletService();

    $svc->credit($user, 500, 'click', 42, 'click earnings');

    $user->refresh();
    expect($user->balance)->toBe(1500);
    expect($user->total_earned)->toBe(500);

    $tx = WalletTransaction::where('user_id', $user->id)->first();
    expect($tx->type)->toBe('credit');
    expect($tx->amount)->toBe(500);
    expect($tx->balance_after)->toBe(1500);
    expect($tx->reference_type)->toBe('click');
    expect($tx->reference_id)->toBe(42);
});

it('debits user balance and rejects when insufficient', function () {
    $user = User::factory()->create(['balance' => 100]);
    $svc = new WalletService();

    expect(fn() => $svc->debit($user, 200, 'payout_hold', null))
        ->toThrow(\RuntimeException::class, 'Insufficient balance');

    $user->refresh();
    expect($user->balance)->toBe(100);
});

it('debits successfully and records transaction', function () {
    $user = User::factory()->create(['balance' => 500]);
    $svc = new WalletService();

    $svc->debit($user, 200, 'payout_hold', 7, 'payout req #7');

    $user->refresh();
    expect($user->balance)->toBe(300);

    $tx = WalletTransaction::where('user_id', $user->id)->first();
    expect($tx->amount)->toBe(-200);
    expect($tx->balance_after)->toBe(300);
});
