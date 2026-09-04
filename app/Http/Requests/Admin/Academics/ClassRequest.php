<?php

namespace App\Http\Requests\Admin\Academics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassRequest extends FormRequest
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
        $shift_id = shiftEnable() && ! empty($this->shift) ? $this->shift : null;
        $rules = [];
        if (generalSetting()->result_type === 'mark') {
            $rules = [
                'name' => ['required', 'max:200', Rule::unique('sm_classes', 'class_name')->where('academic_id', getAcademicId())
                    ->when(shiftEnable() && ! empty($shift_id), function ($query) use ($shift_id) {
                        $query->where('shift_id', $shift_id);
                    })->when(moduleStatusCheck('Branch'), function ($query) {
                        $query->where('branch_id', $this->branch_id);
                    })->where('school_id', auth()->user()->school_id)->ignore($this->id)],
                'section' => 'required',
                'pass_mark' => 'required',
            ];
        } elseif (shiftEnable()) {
            $rules = [
                'name' => ['required', 'max:200', Rule::unique('sm_classes', 'class_name')->where('academic_id', getAcademicId())
                    ->when(shiftEnable() && ! empty($shift_id), function ($query) use ($shift_id) {
                        $query->where('shift_id', $shift_id);
                    })->where('school_id', auth()->user()->school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                        $query->where('branch_id', $this->branch_id);
                    })->ignore($this->id)],
                'section' => 'required',
                'shift' => 'required',
            ];
        } else {
            $rules = [
                'name' => ['required', 'max:200', Rule::unique('sm_classes', 'class_name')
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', auth()->user()->school_id)
                    ->when(moduleStatusCheck('Branch'), function ($query) {
                        $query->where('branch_id', $this->branch_id);
                    })
                    ->ignore($this->id)],
                'section' => 'required',
            ];
        }

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
            $messages['name.unique'] = __('branch::branch.branch_name_unique');
        }

        return $messages;
    }
}
