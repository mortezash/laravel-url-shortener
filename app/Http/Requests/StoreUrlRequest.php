<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrlRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'url' => ['required', 'url', 'max:2048'],
			'expire_days' => ['nullable', 'integer', 'min:1', 'max:365'] // اختیاری، بین 1 تا 365 روز
		];
    }
}
