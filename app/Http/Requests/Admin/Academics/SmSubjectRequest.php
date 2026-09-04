<?php

namespace App\Http\Requests\Admin\Academics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        $rules = [
            'subject_name' => ['required', 'max:200', Rule::unique('sm_subjects', 'subject_name')->where('academic_id', getAcademicId())->where('school_id', auth()->user()->school_id)
                ->when(moduleStatusCheck('Branch'), function ($query) {
                    $query->where('branch_id', $this->branch_id);
                })->ignore($this->id)],
            'subject_type' => 'required',
            'subject_code' => ['sometimes', 'required', 'max:200', Rule::unique('sm_subjects', 'subject_code')->where('academic_id', getAcademicId())->where('school_id', auth()->user()->school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->where('academic_id', getAcademicId())->where('school_id', auth()->user()->school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
        ];

        if (@generalSetting()->result_type === 'mark') {
            $rules += [
                'pass_mark' => 'required',
            ];
        }
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
            $messages['name.unique'] = __('branch::branch.branch_name_unique');
            $messages['subject_code.unique'] = __('branch::branch.branch_name_unique');
        }

        return $messages;
    }
}
