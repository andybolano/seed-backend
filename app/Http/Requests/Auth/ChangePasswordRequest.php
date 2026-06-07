<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string|current_password',
            'password'         => 'required|string|min:8|confirmed|different:current_password',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.different'                 => 'La nueva contraseña debe ser diferente a la actual.',
            'password.confirmed'                 => 'Las contraseñas no coinciden.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
