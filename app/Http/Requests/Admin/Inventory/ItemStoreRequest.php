<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemStoreRequest extends FormRequest
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
            'store_name' => ['required', Rule::unique('sm_item_stores', 'store_name')->where('school_id', auth()->user()->school_id)->when(moduleStatusCheck('Branch'), function ($query) {
                $query->where('branch_id', $this->branch_id);
            })->ignore($this->id)],
            'store_no' => 'required',
            'description' => 'sometimes|nullable',
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
        }

        return $messages;
    }
}
