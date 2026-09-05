<?php

namespace App\Traits;

use App\Models\SmCustomField;
use Illuminate\Support\Str;

trait CustomFields
{
    public function storeFields($model, $fields, $form_name) {}

    public function generateValidateRules($form_name, $model = null): array
    {

        $fields = SmCustomField::where(['form_name' => $form_name])->when(auth()->check(), function ($q): void {
            $q->where('school_id', auth()->user()->school_id);
        })->get();
        $rules = [];
        $custom_fields = ($model && $model->custom_field) ? json_decode($model->custom_field, true) : [];

        if (count($fields) > 0) {
            $is_custom_field_enforced = false;
            
            if ($form_name === 'staff_registration') {
                if (auth()->check()) {
                    $staff_setting = \App\Models\SmStaffRegistrationField::where('school_id', auth()->user()->school_id)
                        ->where('field_name', 'custom_fields')
                        ->first();
                    $is_custom_field_enforced = $staff_setting && $staff_setting->is_required == 1 && $staff_setting->staff_edit == 1;
                }
            } else {
                $is_custom_field_enforced = is_show('custom_field');
            }

            foreach ($fields as $field) {
                $field_rule = [];
                $field_name = str_replace('-', '_', Str::slug($field->label));
                $field->required ? ($is_custom_field_enforced ? array_push($field_rule, 'required') : null) : array_push($field_rule, 'nullable');
                if ($field->type === 'fileInput') {
                    $rules['customF.'.$field_name] = gv($custom_fields, $field_name) ? [] : $field_rule;
                } else {

                    $rules['customF.'.$field_name] = $field_rule;
                }

            }
        }

        return $rules;
    }
}
