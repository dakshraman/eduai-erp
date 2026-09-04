<?php

namespace App\Http\Requests;

use DateTimeImmutable;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShiftRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'name' => 'required',
            'name' => ['required', 'max:200', Rule::unique('shifts', 'name')
                ->where('academic_id', getAcademicId())
                ->where('school_id', auth()->user()->school_id)
                ->when(moduleStatusCheck('Branch'), function ($query) {
                    $query->where('branch_id', $this->branch_id);
                })
                ->ignore($this->id)],
            'start_time' => 'required|date_format:g:i A',
            'end_time' => [
                'required', 'date_format:g:i A',
                function ($attribute, $value, $fail) {
                    $startTimeStr = request()->input('start_time');
                    $endTimeStr = $value;

                    try {
                        $start = DateTimeImmutable::createFromFormat('g:i A', $startTimeStr);
                        $end = DateTimeImmutable::createFromFormat('g:i A', $endTimeStr);

                        if (! $start || ! $end) {
                            return $fail('Invalid time format.');
                        }

                        // If end time is earlier than or equal to start, assume it's on the next day
                        if ($end <= $start) {
                            $end->modify('+1 day');
                        }

                        if ($end <= $start) {
                            $fail('The end time must be greater than the start time.');
                        }

                    } catch (Exception $e) {
                        $fail('Invalid time comparison.');
                    }
                },
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
