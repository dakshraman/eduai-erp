@extends('backEnd.master')
@section('title')
@lang('reports.student_history')
@endsection

@section('mainContent')
<style>
    .check_box_table table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child::before,
    .check_box_table table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child::before {
        left: 8px;
        top: 30px;
        line-height: 18px;
    }

</style>
<input type="text" hidden value="{{ @$clas->class_name }}" id="cls">
<input type="text" hidden value="{{ @$clas->section_name->sectionName->section_name }}" id="sec">
<section class="sms-breadcrumb mb-20 up_breadcrumb">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('reports.student_history')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('reports.reports')</a>
                <a href="#">@lang('reports.student_history')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="main-title">
                                <h3 class="mb-15">@lang('common.select_criteria') </h3>
                            </div>
                        </div>
                    </div>
                    {{ html()->form('POST', route('student_history_search_new'))->attributes([
                        'class' => 'form-horizontal',
                        'files' => true,
                        'enctype' => 'multipart/form-data',
                        'id' => 'search_student',
                    ])->open() }}
                    <div class="row">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                        @if (moduleStatusCheck('Branch'))
                            @include('branch::components.branch_select', [
                                'grid_class' => 'col-lg-4',
                                'mb' => 'mb-15',
                                'branch_id' => isset($branch_id) ? $branch_id : '',
                            ])
                        @endif
                        @include('backEnd.shift.shift_class_section_include', [
                            'div' => shiftEnable() ? 'col-lg-4' : 'col-lg-4',
                            'visiable' => ['shift', 'class'],
                            'required' => ['class'],
                            'title' => ['shift', 'class'],
                            'class_name' => 'class',
                            'selected' => [
                                'shift_id' => @$shift_id,
                                'class_id' => @$class_id,
                            ],
                        ])
                        <div class="col-lg-4 mt-30-md col-md-4">
                            <label class="primary_input_label" for="">{{ __('common.admission_year') }}
                                <span></span>
                            </label>
                            <select class="primary_select {{ $errors->has('current_section') ? ' is-invalid' : '' }}"
                                name="admission_year">
                                <option data-display="@lang('reports.select_admission_year')" value="">
                                    @lang('reports.select_admission_year')</option>
                                @foreach($years as $key => $value)
                                <option value="{{$key}}" {{isset($year)? ($year == $key? 'selected': ''):''}}>{{$key}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-20 text-right">
                        <button type="submit" class="primary-btn small fix-gr-bg">
                            <span class="ti-search pr-2"></span>
                            @lang('common.search')
                        </button>
                    </div>
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>

    @if(isset($students))
    <div class="row mt-40">
        <div class="col-lg-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-lg-6 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-15">@lang('reports.student_report')</h3>
                        </div>
                    </div>
                </div>
    
                <!-- <div class="d-flex justify-content-between mb-20"> -->
                <!-- <button type="submit" class="primary-btn fix-gr-bg mr-20" onclick="javascript: form.action='{{url('student-attendance-holiday')}}'">
                        <span class="ti-hand-point-right pr"></span>
                        mark as holiday
                    </button> -->
    
                <!-- </div> -->
                <div class="row">
                    <div class="col-lg-12">
                        <x-table>
                            <table id="table_id" class="table"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        @if (moduleStatusCheck('Branch'))
                                        <th>@lang('common.branch')</th>
                                        @endif
                                        <th>@lang('student.admission_no')</th>
                                        <th>@lang('common.name')</th>
                                        <th>@lang('student.admission_date')</th>
                                        <th>@lang('reports.class_start_end')</th>
                                        <th>@lang('reports.session_start_end')</th>
                                        <th>@lang('common.mobile')</th>
                                        <th>@lang('student.guardian_name')</th>
                                        <th>@lang('student.guardian_phone')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            @if (moduleStatusCheck('Branch'))
                                            <td>{{$student->branch?->branch_name}}</td>
                                            @endif
                                            <td>{{$student->admission_no}}</td>
                                            <td>{{$student->first_name.' '.$student->last_name}}</td>
                                            <td data-sort="{{strtotime($student->admission_date)}}">
                                                {{$student->admission_date != ""? dateConvert($student->admission_date):''}}
                                            </td>
                                            <td>{{$student->recordClass !="" ?$student->recordClass->class->class_name : ''}}
                                            </td>
                                            <td>{{$student->sessions}}</td>
                                            <td>{{$student->mobile}}</td>
                                            <td>{{$student->parents !=""?$student->parents->guardians_name:""}}</td>
                                            <td>{{$student->parents !=""?$student->parents->guardians_mobile:""}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    </div>
</section>

@endsection
@include('backEnd.partials.data_table_js')
