<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoneyPlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'charge' => 'required|numeric|min:0.01',
        ];
    }

    public function attributes(): array
    {
        return [
            'charge' => 'Số tiền',
        ];
    }

    public function messages(): array
    {
        return [
            'charge.required' => 'Số tiền là bắt buộc',
            'charge.numeric' => 'Số tiền phải là số',
            'charge.min' => 'Số tiền phải lớn hơn 0',
        ];
    }
}
