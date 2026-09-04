<?php

namespace App\Http\Requests\Admin\FeesCollection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmFeesDiscountRequest extends FormRequest
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
        if (moduleStatusCheck('University')) {
            $rules = [
                'name' => ['required', 'max:200', Rule::unique('sm_fees_discounts')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
                'code' => ['required', Rule::unique('sm_fees_discounts')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
                'amount' => 'required|min:0',
                'description' => 'nullable|max:200',
            ];
        }

        elseif (directFees()) {
            $rules = [
                'name' => ['required', 'max:200', Rule::unique('sm_fees_discounts')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
                'code' => ['required', Rule::unique('sm_fees_discounts')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
                'amount' => 'required|min:0',
                'description' => 'nullable|max:200',
            ];
        } else {
            $rules = [
                'name' => ['required', 'max:200', Rule::unique('sm_fees_discounts')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
                'code' => ['required', Rule::unique('sm_fees_discounts')->where('school_id', $school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
                'amount' => 'required|min:0',
                'type' => 'required',
                'description' => 'nullable|max:200',
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
        }

        return $messages;
    }
}