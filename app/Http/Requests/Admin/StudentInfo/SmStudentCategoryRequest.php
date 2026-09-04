<?php

namespace App\Http\Requests\Admin\StudentInfo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SmStudentCategoryRequest extends FormRequest
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
        $school_id = auth()->user()->school_id;
        $hasBranchColumn = Schema::hasColumn('sm_student_categories', 'branch_id');

        $rules = [
            'category' => ['required', Rule::unique('sm_student_categories', 'category_name')->where('school_id', $school_id)->when(moduleStatusCheck('Branch') && $hasBranchColumn, function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
        ];

        if (moduleStatusCheck('Branch')) {
            $rules['branch_id'] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
        ];

        if (moduleStatusCheck('Branch')) {
            $messages = [
                'branch_id.required' => 'The branch field is required.',
            ];
        }

        return $messages;
    }
}
