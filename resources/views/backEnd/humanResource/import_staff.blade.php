@extends('backEnd.master')
@section('title')
    @lang('hr.staff_import')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('hr.staff_import')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('hr.human_resource')</a>
                    <a href="#">@lang('hr.staff_import')</a>
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
                                __('Your CSV data should be in the format download file. The first line of your CSV file should be the column headers as in the table example. Also make sure that your file is UTF-8 to avoid unnecessary encoding problems.'),
                                __('Duplicate "Staff ID" (unique in section) rows will not be imported and would be number.'),
                                'If the column you are trying to import is Role make sure that is Role formatted : ( ' . 
                                    $roles->map(fn($role) => '"' . $role->name . '"')->implode(', ') . ' )',
                                'If the column you are trying to import is Department make sure that is Department formatted : ( ' . 
                                    $departments->map(fn($department) => '"' . $department->name . '"')->implode(', ') . ' ).',
                                count($designations) > 0
                                    ? 'If the column you are trying to import is Designation make sure that is Designation formatted : ' . 
                                        $designations->map(fn($designation) => '"' . $designation->title . '"')->implode(', ') . '.'
                                    : '',
                                'If the column you are trying to import is Gender make sure that is Gender formatted : ( ' .
                                    $genders->map(fn($gender) => $gender->id . '=' . $gender->base_setup_name)->implode(', ') . ' ).',
                                'Marital Status. ("married", "unmarried").',
                                'Contract Type. ("permanent", "contract").',
                            ];
                        @endphp
                        
                        <x-import-data
                            :url="route('staff-bulk-store')"
                            sample="{{ url('/public/backEnd/bulksample/staffs.xlsx') }}"
                            :instructions="$ins"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
