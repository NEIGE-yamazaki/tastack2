<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */

public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class)->ignore($this->user()->id),
        ],
        'account_id' => [
            'required',
            'alpha_num',
            'max:10',
            Rule::unique(User::class)->ignore($this->user()->id),
        ],
        'button_layout' => ['required', Rule::in(['menu_above', 'menu_under'])], // ← 追加
        'google_calendar_color' => ['nullable', Rule::in([
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'
        ])],

    ];
}
    
}
