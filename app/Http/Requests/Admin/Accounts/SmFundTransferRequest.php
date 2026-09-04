<?php

namespace App\Http\Requests\Admin\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class SmFundTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return userPermission('fund-transfer-store');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|gt:0',
            'purpose' => 'required',
            'from_payment_method' => 'required',
            'to_payment_method' => 'required',
            'from_bank_name' => 'required_if:from_payment_method,3|nullable|integer',
            'to_bank_name' => 'required_if:to_payment_method,3|nullable|integer',
        ];
    }
}
