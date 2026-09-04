@extends('backEnd.master')
@section('title')
    @lang('student.student_import')
@endsection
@push('css')
    <style>
        .input-right-icon button.primary-btn-small-input {
            top: 8px !important;
            right: 11px !important;
        }
    </style>
@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.student_import')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_admission')</a>
                    <a href="#">@lang('student.student_import')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">

                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">@lang('student.student_import')</h3>
                            </div>
                        </div>

                        @php
                            $ins = [
                                'Your CSV data should be in the format of the downloaded file. The first line should be column headers as in the example table. Ensure the file is UTF-8 encoded to avoid encoding issues.',
                                'If any column is a date field, format it as Y-m-d (e.g., 2018-06-06).',
                                'Duplicate "Roll Number" (must be unique within a section) rows will not be imported. You can verify usage from the student report by searching Class & Section.',
                                'Duplicate "Guardian Email & Guardian Phone" rows will not be imported. You can verify usage from the student report by searching Class & Section.',
                                'For student "Gender", use ID values: ' . collect($genders)->map(fn($gender) => "{$gender->id}={$gender->base_setup_name}")->implode(', ') . '.',
                                'For student "Blood Group", use ID values: ' . collect($blood_groups)->map(fn($bg) => "{$bg->id}={$bg->base_setup_name}")->implode(', ') . '.',
                                'For student "Religion", use ID values: ' . collect($religions)->map(fn($rel) => "{$rel->id}={$rel->base_setup_name}")->implode(', ') . '.',
                                'For guardian relation, use: F=Father, M=Mother, O=Other.',
                                'Please follow the date format (e.g., 2020-06-15) for both Date of Birth and Admission Date.',
                            ];
                            if (shiftEnable() && !moduleStatusCheck('University')) {
                                $ins[] = 'To store data shift-wise, selected shift; otherwise, leave it blank';
                            }
                            if(moduleStatusCheck('Branch')){
                                $ins[] = 'For Branch, select the branch id from the branch list.';
                            }
                        @endphp
                        
                        <x-student-import-data
                            :url="route('student_bulk_store')"
                            sample="{{ url('/public/backEnd/bulksample/students.xlsx') }}"
                            :instructions="$ins"
                            :sessions="$sessions"
                            :classes="$classes"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
