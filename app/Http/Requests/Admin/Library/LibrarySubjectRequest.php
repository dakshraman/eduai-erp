<?php

namespace App\Http\Requests\Admin\Library;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LibrarySubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $school_id = auth()->user()->school_id;

        $rules = [
            'subject_name' => ['required', 'max:30', Rule::unique('library_subjects')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
            'category' => 'required',
            'subject_code' => ['required', 'max:30', Rule::unique('library_subjects')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
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
