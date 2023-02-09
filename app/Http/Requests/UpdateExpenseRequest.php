<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateExpenseRequest extends FormRequest
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
            'amount' => 'required|integer',
            'item_type' => 'required|string',
            'unit_type' => 'required|string',
            'cost' => 'required|integer',
            'realized' => 'nullable|integer',
            'tw' => 'required|integer',
            'activity_id' => 'required|exists:activities,id',
        ];
    }
}
