<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateExpenseRequest extends FormRequest
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
            'expense_type' => 'required|string',
            'item_type' => 'required|string',
            'detailType_id' => 'required|exists:detail_type,id',
            'unit_type' => 'required|string',
            'cost' => 'required|integer',
            'tw' => 'required|integer',
            'activity_id' => 'required|exists:activities,id',
            'program_id' => 'required|exists:programs,id',
        ];
    }
}
