<?php

namespace App\Http\Requests\Admin\AdminSection;

use Illuminate\Foundation\Http\FormRequest;

class SmStudentCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxFileSize = generalSetting()->file_size * 1024;

        $rules = [
            'name' => 'required|max:50',
            'header_left_text' => 'nullable',
            'date' => 'nullable|date',
            'body' => 'nullable',
            'footer_left_text' => 'nullable',
            'footer_center_text' => 'nullable',
            'footer_right_text' => 'nullable',
            'student_photo' => 'nullable',
            'layout' => 'required',
            'body_font_size' => 'required',
            'height' => 'required',
            'width' => 'required',
            'file' => 'mimes:jpg,jpeg,png|max:'.$maxFileSize,
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
