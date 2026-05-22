<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url:http,https', 'max:2048'],
            'custom_alias' => ['nullable', 'alpha_dash', 'min:3', 'max:32', 'unique:short_links,slug'],
            'password' => ['nullable', 'string', 'min:4', 'max:64'],
        ];
    }
}
