<?php

namespace App\Http\Requests\Admin\Academics;

use Illuminate\Foundation\Http\FormRequest;

class SmAssignClassTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'class'   => 'required|exists:sm_classes,id',
            'section' => 'required|exists:sm_sections,id',
            'teacher' => 'required|exists:sm_staffs,id',
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
