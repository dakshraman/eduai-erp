<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamTypeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'exam_type_title' => ['required', 'max:50', Rule::unique('sm_exam_types', 'title')->where('academic_id', getAcademicId())->where('school_id', auth()->user()->school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
            'average_mark' => 'required_if:is_average,yes|nullable|numeric|min:0',
        ];

        if (moduleStatusCheck('Branch')) {
            $rules['branch_id'] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        if (moduleStatusCheck('Branch')) {
            $messages['branch_id.required'] = __('branch::branch.branch_required');
            $messages['exam_type_title.unique'] = __('branch::branch.branch_name_unique');
        }

        return $messages;
    }
}
