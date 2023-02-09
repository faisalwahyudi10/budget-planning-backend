<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RealizationActivityRequest extends FormRequest
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
            'activity_realized_tw1' => 'nullable|integer',
            'activity_realized_tw2' => 'nullable|integer',
            'activity_realized_tw3' => 'nullable|integer',
            'activity_realized_tw4' => 'nullable|integer',

            'document_realized_tw1' => 'nullable|integer',
            'document_realized_tw2' => 'nullable|integer',
            'document_realized_tw3' => 'nullable|integer',
            'document_realized_tw4' => 'nullable|integer',
        ];
    }
}
