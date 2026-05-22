<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('link')->user_id;
    }

    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url:http,https', 'max:2048'],
            'password' => ['nullable', 'string', 'min:4', 'max:64'],
            'status' => ['required', 'in:active,disabled'],
        ];
    }
}
