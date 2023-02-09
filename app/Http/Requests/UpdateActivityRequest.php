<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateActivityRequest extends FormRequest
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

            'activity_budget_tw1' => 'required|integer',
            'activity_budget_tw2' => 'required|integer',
            'activity_budget_tw3' => 'required|integer',
            'activity_budget_tw4' => 'required|integer',

            'activity_realized_tw1' => 'nullable|integer',
            'activity_realized_tw2' => 'nullable|integer',
            'activity_realized_tw3' => 'nullable|integer',
            'activity_realized_tw4' => 'nullable|integer',

            'document_plan_tw1' => 'required|integer',
            'document_plan_tw2' => 'required|integer',
            'document_plan_tw3' => 'required|integer',
            'document_plan_tw4' => 'required|integer',

            'document_realized_tw1' => 'nullable|integer',
            'document_realized_tw2' => 'nullable|integer',
            'document_realized_tw3' => 'nullable|integer',
            'document_realized_tw4' => 'nullable|integer',

            'target' => 'required|string|max:255|',
            'indicator' => 'required|string|max:255|',
            'program_id' => 'required|exists:programs,id',
        ];
    }
}
