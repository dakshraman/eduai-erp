<?php

namespace App\Http\Requests\Admin\AdminSection;

use Illuminate\Foundation\Http\FormRequest;

class SmAdmissionQuerySearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:date_from',
            'source' => 'nullable',
            'status' => 'required',
        ];

        if (moduleStatusCheck('Branch')) {
            $rules['branch_id'] = 'nullable|exists:branches,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        if (moduleStatusCheck('Branch')) {
            $messages['branch_id.exists'] = __('branch::branch.branch_required');
        }

        return $messages;
    }
}
