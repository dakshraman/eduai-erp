@extends('backEnd.master')
@section('title')
    @lang('exam.add_marks_import')
@endsection

@push('css')
    <style>
        input.primary_input_field.marks_input {
            min-width: 101px;
        }
    </style>
@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('exam.add_marks_import') </h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('exam.examination')</a>
                    <a href="{{ route('marks_register') }}">@lang('exam.marks_register')</a>
                    <a href="#">@lang('exam.add_marks_import')</a>
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
                                <h3 class="mb-0 mr-30">@lang('exam.add_marks_import')</h3>
                            </div>
                        </div>

                        @php
                            $ins = [
                                'If the Exam Attendance setting is enabled, please create the Exam Attendance first.',
                                'All information must be included in a single file.',
                                'The first row of the file must contain the column headers (field titles).',
                                'Before uploading the file, ensure Exam, Class, Section, and Subject are selected.',
                                'Admission No and Exam Type are required fields.',
                                'Include the appropriate number of marks columns based on your exam type.',
                                'The exam type will be auto-identified if you use the same title while creating the exam (e.g., Written, MCQ, Practical)',
                                'Add Teacher Remarks only when necessary.',
                            ];

                            if (shiftEnable()) {
                                $ins[] = 'To store data shift-wise, selected shift; otherwise, leave it blank';
                            }
                        @endphp
                        
                        <x-register-import-data
                            :url="route('marks-register-import-store')"
                            sample="{{ url('/public/backEnd/bulkxl/student_attendance.xlsx') }}"
                            :instructions="$ins"
                            :exams="$exams"
                            :classes="$classes"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
