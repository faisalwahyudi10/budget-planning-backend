<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|',
            'nik' => ['required', 'string', 'max:255', Rule::unique('employees')->ignore($this->id)],
            'position' => 'required|string',
            'gender' => 'required|string',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees')->ignore($this->id)],
            'address' => 'required|string|max:255',
        ];
    }
}
