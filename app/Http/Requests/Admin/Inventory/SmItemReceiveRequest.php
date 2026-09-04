<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class SmItemReceiveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $totalValues = [];
        $subTotal = 0;
        $subTotalQuantity = 0;

        foreach ((array) $this->input('item_id', []) as $index => $itemId) {
            $unitPrice = (float) ($this->input("unit_price.$index") ?? 0);
            $quantity = (float) ($this->input("quantity.$index") ?? 0);
            $lineTotal = $unitPrice * $quantity;

            $totalValues[$index] = $lineTotal;
            if ($itemId !== null && $itemId !== '') {
                $subTotal += $lineTotal;
                $subTotalQuantity += $quantity;
            }
        }

        $totalPaid = $this->boolean('full_paid')
            ? $subTotal
            : (float) ($this->input('totalPaidValue') ?: $this->input('totalPaid', 0));

        $this->merge([
            'totalValue' => $totalValues,
            'subTotal' => $subTotal,
            'subTotalValue' => $subTotal,
            'subTotalQuantity' => $subTotalQuantity,
            'subTotalQuantityValue' => $subTotalQuantity,
            'totalPaid' => $totalPaid,
            'totalPaidValue' => $totalPaid,
            'totalDueValue' => max($subTotal - $totalPaid, 0),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'bank_id' => 'required_if:payment_method,Bank',
            'expense_head_id' => 'required',
            'supplier_id' => 'required',
            'store_id' => 'required',
            'reference_no' => 'sometimes|nullable',
            'receive_date' => 'required|date',
            'payment_method' => 'required',
            'description' => 'sometimes|nullable',
            'item_id' => 'required|array',
            'item_id.*' => 'required',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|gt:0',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|gt:0',
            'totalValue' => 'required|array',
            'totalValue.*' => 'required|numeric|min:0',
            'total' => 'sometimes|nullable|array',
            'subTotalQuantity' => 'sometimes|nullable',
            'subTotal' => 'required|numeric|min:0',
            'totalPaid' => 'numeric|min:0|lte:subTotal',
            'totalDueValue' => 'required|numeric|min:0',
        ];
    }
}
