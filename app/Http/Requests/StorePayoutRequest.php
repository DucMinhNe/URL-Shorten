<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $minVnd = (int) app(\App\Services\SettingService::class)->get('min_payout_vnd', 100000);
        $maxAmount = $this->user()->balance;
        return [
            'amount' => ['required', 'integer', "min:{$minVnd}", "max:{$maxAmount}"],
            'method' => ['required', 'in:momo,zalo,paypal'],
            'account_info' => ['required', 'string', 'max:255'],
        ];
    }

    /** Thông báo rõ ràng khi số dư chưa đạt mức rút tối thiểu (max < min → lỗi khó hiểu). */
    public function withValidator($validator): void
    {
        $minVnd = (int) app(\App\Services\SettingService::class)->get('min_payout_vnd', 100000);
        if ($this->user()->balance < $minVnd) {
            $validator->after(fn ($v) => $v->errors()->add(
                'amount',
                'Số dư chưa đạt mức rút tối thiểu ('.number_format($minVnd).'đ).'
            ));
        }
    }
}
