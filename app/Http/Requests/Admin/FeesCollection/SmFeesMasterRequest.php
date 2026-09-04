<?php

namespace App\Http\Requests\Admin\FeesCollection;

use Illuminate\Foundation\Http\FormRequest;

class SmFeesMasterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make thcheckis request.
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

        if (moduleStatusCheck('University')) {
            $rules = [
                'name' => 'required',
                'amount' => 'required',
            ];
        } elseif (directFees()) {
            $rules = [
                'name' => 'required',
                'amount' => 'required',
                'class' => 'required',
                'section_id' => 'required',
                'unPercentage' => 'required',
                'title.*' => 'required',
                'due_date.*' => 'required',
                'unPercentage.*' => 'required|numeric',
                'totalInstallmentAmount' => 'required|same:amount',
            ];
        } else {
            $rules = [
                'fees_type' => 'required',
                'date' => 'required|date',
                'amount' => 'required',
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
        $messages['title.*.required'] = 'The installment title is required';
        $messages['due_date.*.required'] = 'The installment due date is required';
        $messages['unPercentage.*.required'] = 'The installment amount is required';
        $messages['unPercentage.*.numeric'] = 'The installment amount must be a number';

        return $messages;
    }
}