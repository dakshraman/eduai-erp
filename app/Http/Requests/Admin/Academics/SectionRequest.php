<?php

namespace App\Http\Requests\Admin\Academics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectionRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('sm_sections', 'section_name')
                    ->when(
                        moduleStatusCheck('University'),
                        function ($query): void {
                            $query->where('un_academic_id', getAcademicId());
                        },
                        function ($query): void {
                            $query->where('academic_id', getAcademicId());
                        }
                    )
                    ->where('school_id', auth()->user()->school_id)
                    ->when(moduleStatusCheck('Branch'), function ($query) {
                        $query->where('branch_id', $this->branch_id);
                    })
                    ->ignore($this->id),
            ],
        ];

        if (moduleStatusCheck('Branch')) {
            $rules['branch_id'] = ['required'];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];

        if (moduleStatusCheck('Branch')) {
            $messages['branch_id.required'] = __('branch::branch.branch_required');
            $messages['name.unique'] = __('branch::branch.branch_name_unique');
        }

        return $messages;
    }
}
