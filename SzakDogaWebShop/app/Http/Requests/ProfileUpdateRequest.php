<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function messages(): array
    {
        return [
            'name.required' => 'Add meg a nevedet.',
            'name.string' => 'A név szöveges érték legyen.',
            'name.max' => 'A név legfeljebb 255 karakter lehet.',
            'email.required' => 'Add meg az e-mail-címedet.',
            'email.string' => 'Az e-mail-cím szöveges érték legyen.',
            'email.lowercase' => 'Az e-mail-címet kisbetűkkel add meg.',
            'email.email' => 'Adj meg egy érvényes e-mail-címet.',
            'email.max' => 'Az e-mail-cím legfeljebb 255 karakter lehet.',
            'email.unique' => 'Ezzel az e-mail-címmel már létezik fiók.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
        ];
    }
}
