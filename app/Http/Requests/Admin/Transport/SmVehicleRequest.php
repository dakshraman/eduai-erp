<?php

namespace App\Http\Requests\Admin\Transport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmVehicleRequest extends FormRequest
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

        $rules = [
            'vehicle_number' => [
                'required',
                'max:200',
                Rule::unique('sm_vehicles', 'vehicle_no')
                    ->where(function ($query) use ($school_id) {
                        return $query->where('school_id', $school_id);
                    })->when(moduleStatusCheck('Branch'), function ($query) {
                        $query->where('branch_id', $this->branch_id);
                    })
                    ->ignore($this->id),
            ],
            'vehicle_model' => 'required|max:200',
            'year_made' => 'sometimes|nullable|max:10',
            'note' => 'sometimes|nullable',
            'driver_id' => 'required',
        ];

        if (moduleStatusCheck('Branch')) {
            $rules += [
                'branch_id' => 'required',
            ];
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'driver_id' => 'driver',
        ];
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
