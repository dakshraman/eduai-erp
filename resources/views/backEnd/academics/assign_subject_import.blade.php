@extends('backEnd.master')
@section('title')
    @lang('academics.assign_subject_import')
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
                <h1>@lang('academics.assign_subject_import')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('academics.academics')</a>
                    <a href="{{ route('assign_subject') }}">@lang('academics.assign_subject')</a>
                    <a href="{{ route('assign_subject_import') }}">@lang('academics.assign_subject_import')</a>
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
                                <h3 class="mb-0 mr-30">@lang('academics.assign_subject_import')</h3>
                            </div>
                        </div>

                        @php
                            $ins = [
                                'All information must be included in a single file.',
                                'The first row of the file must contain the column headers (field titles).',
                                'Before uploading the file, ensure Class and Section are selected.',
                                'Subject entry must include a subject code. Teacher entry must include either an email address or a staff number.',
                            ];

                            if (shiftEnable()) {
                                $ins[] = 'To store data shift-wise, selected shift; otherwise, leave it blank.';
                            }

                            if (moduleStatusCheck('Branch')) {
                                $ins[] = 'If the Branch module is active in your system, selecting a branch is mandatory.';
                            }
                        @endphp
                        
                        <x-subject-import-data
                            :url="route('assign_subject_import_store')"
                            sample="{{ url('/public/backEnd/bulkxl/assign_subject.xlsx') }}"
                            :instructions="$ins"
                            :classes="$classes"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
