<?php

namespace App\Http\Requests\Admin\Dormitory;

use Illuminate\Foundation\Http\FormRequest;

class SmDormitoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'dormitory_name' => 'required|max:200',
            'type' => 'required',
            'address' => 'required',
            'intake' => 'required|integer|min:0',
            'description' => 'sometimes|nullable|max:200',
        ];

        if (moduleStatusCheck('Branch')) {
            $rules += [
                'branch_id' => 'required',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        if (moduleStatusCheck('Branch')) {
            $messages['branch_id.required'] = __('branch::branch.branch_required');
        }

        return $messages;
    }
}
