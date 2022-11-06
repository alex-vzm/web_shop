<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordFormRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;  // auth()->guest();
    }


    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:1|confirmed',
        ];
    }
}
