@extends('backEnd.master')
@section('title')
    @lang('student.subject_attendance_report')
@endsection

@section('mainContent')
    @push('css')
        <style>
            #table_id1 {
                border: 1px solid var(--border_color);

            }

            #table_id1 td {
                border: 1px solid var(--border_color);
                text-align: center;
            }

            #table_id1 th {
                border: 1px solid var(--border_color);
                text-align: center;
            }

            .main-wrapper {
                display: block;
                width: auto;
                align-items: stretch;
            }

            .main-wrapper {
                display: block;
                width: auto;
                align-items: stretch;
            }

            #main-content {
                width: auto;
            }

            #table_id1 td {
                border: 1px solid var(--border_color);
                text-align: center;
                padding: 7px;
                flex-wrap: nowrap;
                white-space: nowrap;
                font-size: 11px
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: #828bb2;
                height: 5px;
                border-radius: 0;
            }

            .table-responsive::-webkit-scrollbar {
                width: 5px;
                height: 5px
            }

            .table-responsive::-webkit-scrollbar-track {
                height: 5px !important;
                background: #ddd;
                border-radius: 0;
                box-shadow: inset 0 0 5px grey
            }

            hr {
                margin: 0px;
            }
        </style>
    @endpush
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.subject_attendance_report')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_information')</a>
                    <a href="#">@lang('student.subject_attendance_report')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('common.select_criteria')</h3>
                                </div>
                            </div>
                        </div>
                        {{ html()->form('POST', route('subject-attendance-report-search'))->attributes([
                            'class' => 'form-horizontal',
                            'files' => true,
                            'enctype' => 'multipart/form-data',
                            'id' => 'search_student',
                        ])->open() }}
                        <div class="row">
                            @php $current_month = date('m'); @endphp
                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            @if (moduleStatusCheck('Branch'))
                                @include('branch::components.branch_select', [
                                    'grid_class' => 'col-lg-3',
                                    // 'mb' => 'mt-25',
                                    'branch_id' => @$branch_id ?? '',
                                ])
                            @endif
                            @if(moduleStatusCheck('University'))

                                @includeIf('university::common.session_faculty_depart_academic_semester_level',['required'=>['USN', 'UF', 'UD', 'UA', 'US', 'USL'], 'hide'=>['USUB']])
                                <div class="col-lg-3 mt-15">
                                    <label class="primary_input_label" for="">{{ __('common.month') }}  <span class="text-danger"> *</span></label>
                                    <select class="primary_select {{ $errors->has('month') ? ' is-invalid' : '' }}"
                                            name="month">
                                        <option data-display="Select Month *" value="">@lang('student.select_month') *</option>
                                        <option value="01"
                                                {{ isset($month) ? ($month == '01' ? 'selected' : '') : ($current_month == '01' ? 'selected' : '') }}>
                                            @lang('student.january')</option>
                                        <option value="02"
                                                {{ isset($month) ? ($month == '02' ? 'selected' : '') : ($current_month == '02' ? 'selected' : '') }}>
                                            @lang('student.february')</option>
                                        <option value="03"
                                                {{ isset($month) ? ($month == '03' ? 'selected' : '') : ($current_month == '03' ? 'selected' : '') }}>
                                            @lang('student.march')</option>
                                        <option value="04"
                                                {{ isset($month) ? ($month == '04' ? 'selected' : '') : ($current_month == '04' ? 'selected' : '') }}>
                                            @lang('student.april')</option>
                                        <option value="05"
                                                {{ isset($month) ? ($month == '05' ? 'selected' : '') : ($current_month == '05' ? 'selected' : '') }}>
                                            @lang('student.may')</option>
                                        <option value="06"
                                                {{ isset($month) ? ($month == '06' ? 'selected' : '') : ($current_month == '06' ? 'selected' : '') }}>
                                            @lang('student.june')</option>
                                        <option value="07"
                                                {{ isset($month) ? ($month == '07' ? 'selected' : '') : ($current_month == '07' ? 'selected' : '') }}>
                                            @lang('student.july')</option>
                                        <option value="08"
                                                {{ isset($month) ? ($month == '08' ? 'selected' : '') : ($current_month == '08' ? 'selected' : '') }}>
                                            @lang('student.august')</option>
                                        <option value="09"
                                                {{ isset($month) ? ($month == '09' ? 'selected' : '') : ($current_month == '09' ? 'selected' : '') }}>
                                            @lang('student.september')</option>
                                        <option value="10"
                                                {{ isset($month) ? ($month == '10' ? 'selected' : '') : ($current_month == '10' ? 'selected' : '') }}>
                                            @lang('student.october')</option>
                                        <option value="11"
                                                {{ isset($month) ? ($month == '11' ? 'selected' : '') : ($current_month == '11' ? 'selected' : '') }}>
                                            @lang('student.november')</option>
                                        <option value="12"
                                                {{ isset($month) ? ($month == '12' ? 'selected' : '') : ($current_month == '12' ? 'selected' : '') }}>
                                            @lang('student.december')</option>
                                    </select>
                                    @if ($errors->has('month'))
                                        <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('month') }}
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-3 mt-15  ">
                                    <label class="primary_input_label" for="">{{ __('common.year') }}  <span class="text-danger"> *</span></label>
                                    <select class="primary_select form-control{{ $errors->has('year') ? ' is-invalid' : '' }}"
                                            name="year">
                                        <option data-display="Select Year *" value="">@lang('student.select_year') *</option>
                                        @php
                                            $current_year = date('Y');
                                            $ini = date('y');
                                            $limit = $ini + 30;
                                        @endphp
                                        @for ($i = $ini; $i <= $limit; $i++)
                                            <option value="{{ $current_year }}"
                                                    {{ isset($year) ? ($year == $current_year ? 'selected' : '') : (date('Y') == $current_year ? 'selected' : '') }}>
                                                {{ $current_year-- }}</option>
                                        @endfor
                                    </select>
                                    @if ($errors->has('year'))
                                        <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('year') }}
                                    </span>
                                    @endif
                                </div>
                            @else
                            @include('backEnd.common.search_criteria', [
                                'div' => shiftEnable() ? 'col-lg-3' : 'col-lg-3',
                                'mt' => 'mt-30-md',
                                'visiable' => ['shift', 'class', 'section'],
                                'required' => ['class', 'section'],
                                'title' => [],
                                'class_name' => 'class',
                                'section_name' => 'section',
                                'selected' => [
                                    'shift_id' => @$shift_id,
                                    'class_id' => @$class_id,
                                    'section_id' => @$section_id,
                                ],
                            ])


                            <div class="col-lg-3 mt-30-md">
                                <label class="primary_input_label" for="">Select Month
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="primary_select {{ $errors->has('month') ? ' is-invalid' : '' }}"
                                    name="month">
                                    <option data-display="Select Month *" value="">@lang('student.select_month') *</option>
                                    <option value="01"
                                        {{ isset($month) ? ($month == '01' ? 'selected' : '') : ($current_month == '01' ? 'selected' : '') }}>
                                        @lang('student.january')</option>
                                    <option value="02"
                                        {{ isset($month) ? ($month == '02' ? 'selected' : '') : ($current_month == '02' ? 'selected' : '') }}>
                                        @lang('student.february')</option>
                                    <option value="03"
                                        {{ isset($month) ? ($month == '03' ? 'selected' : '') : ($current_month == '03' ? 'selected' : '') }}>
                                        @lang('student.march')</option>
                                    <option value="04"
                                        {{ isset($month) ? ($month == '04' ? 'selected' : '') : ($current_month == '04' ? 'selected' : '') }}>
                                        @lang('student.april')</option>
                                    <option value="05"
                                        {{ isset($month) ? ($month == '05' ? 'selected' : '') : ($current_month == '05' ? 'selected' : '') }}>
                                        @lang('student.may')</option>
                                    <option value="06"
                                        {{ isset($month) ? ($month == '06' ? 'selected' : '') : ($current_month == '06' ? 'selected' : '') }}>
                                        @lang('student.june')</option>
                                    <option value="07"
                                        {{ isset($month) ? ($month == '07' ? 'selected' : '') : ($current_month == '07' ? 'selected' : '') }}>
                                        @lang('student.july')</option>
                                    <option value="08"
                                        {{ isset($month) ? ($month == '08' ? 'selected' : '') : ($current_month == '08' ? 'selected' : '') }}>
                                        @lang('student.august')</option>
                                    <option value="09"
                                        {{ isset($month) ? ($month == '09' ? 'selected' : '') : ($current_month == '09' ? 'selected' : '') }}>
                                        @lang('student.september')</option>
                                    <option value="10"
                                        {{ isset($month) ? ($month == '10' ? 'selected' : '') : ($current_month == '10' ? 'selected' : '') }}>
                                        @lang('student.october')</option>
                                    <option value="11"
                                        {{ isset($month) ? ($month == '11' ? 'selected' : '') : ($current_month == '11' ? 'selected' : '') }}>
                                        @lang('student.november')</option>
                                    <option value="12"
                                        {{ isset($month) ? ($month == '12' ? 'selected' : '') : ($current_month == '12' ? 'selected' : '') }}>
                                        @lang('student.december')</option>
                                </select>
                                @if ($errors->has('month'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('month') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-3 mt-30-md @if(shiftEnable()) mt-20 @endif ">
                                <label class="primary_input_label" for="">Select Year
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="primary_select form-control{{ $errors->has('year') ? ' is-invalid' : '' }}"
                                    name="year">
                                    <option data-display="Select Year *" value="">@lang('student.select_year') *</option>
                                    @php
                                        $current_year = date('Y');
                                        $ini = date('y');
                                        $limit = $ini + 30;
                                    @endphp
                                    @for ($i = $ini; $i <= $limit; $i++)
                                        <option value="{{ $current_year }}"
                                            {{ isset($year) ? ($year == $current_year ? 'selected' : '') : (date('Y') == $current_year ? 'selected' : '') }}>
                                            {{ $current_year-- }}</option>
                                    @endfor
                                </select>
                                @if ($errors->has('year'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('year') }}
                                    </span>
                                @endif
                            </div>

                            @endif
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
        </div>
    </section>
    @if (isset($attendances))
        <section class="student-attendance">
            <div class="container-fluid p-0">
                <div class="white-box mt-40">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title mb-15">
                                <h3 class="mb-0">@lang('student.subject_attendance_report')
                                    <small> <span class="text-success">P:<span id="total_present"></span></span>
                                        <span class="text-warning">L:<span id="total_late"></span></span>
                                        <span class="text-danger">A:<span id="total_absent"></span></span>
                                        <span class="text-info">F:<span id="total_halfday"></span></span>
                                        <span class="text-dark">H:<span id="total_holiday"></span></span>
                                        <span class="text-primary">Le:<span id="total_leave"></span></span> </small>
                                </h3>
                            </div>
                        </div>
                        <div class="col-lg-6 no-gutters">
                            @if (userPermission(536))
                                <a href="{{ route('subject-attendance/print', [$class_id, $section_id, $month, $year, shiftEnable() ? $shift_id : '', moduleStatusCheck('Branch') ? $branch_id : '']) }}"
                                    class="primary-btn small fix-gr-bg pull-right" target="_blank"><i class="ti-printer">
                                    </i>@lang('common.print')</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="lateday d-flex flex-wrap">
                                <div class="mr-3 mb-10">@lang('student.present'): <span class="text-success">P</span></div>
                                <div class="mr-3 mb-10">@lang('student.late'): <span class="text-warning">L</span></div>
                                <div class="mr-3 mb-10">@lang('student.absent'): <span class="text-danger">A</span></div>
                                <div class="mr-3 mb-10">@lang('student.half_day'): <span class="text-info">F</span></div>
                                <div class="mr-3 mb-10">@lang('student.holiday'): <span class="text-dark">H</span></div>
                                <div class="mr-3 mb-10">@lang('student.leave'): <span class="text-primary">Le</span></div>
                                <div class="mr-3 mb-10">@lang('student.weekend'): <span class="text-muted">W</span></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        @php
                            $weekendDayNames = getWeekendDays(Auth::user()->school_id);
                        @endphp

                        <div>
                            <div class="row" style="padding:0 15px">
                                <div class="table-responsive">
                                    <table id="table_id1" style="margin-bottom:25px" class="table table-responsive"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="6%">@lang('common.name')</th>
                                                <th width="6%">@lang('student.admission_no')</th>
                                                <th width="3%">P</th>
                                                <th width="3%">L</th>
                                                <th width="3%">A</th>
                                                <th width="3%">F</th>
                                                <th width="3%">H</th>
                                                <th width="3%">Le</th>
                                                <th width="2%">%</th>
                                                @for ($i = 1; $i <= $days; $i++)
                                                    <th width="3%" class="{{ $i <= 18 ? 'all' : 'none' }}">
                                                        {{ $i }} <br>
                                                        @php
                                                            $date = $year . '-' . $month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                            echo date('D', strtotime($date));
                                                        @endphp
                                                    </th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total_grand_present = 0;
                                                $total_late = 0;
                                                $total_absent = 0;
                                                $total_holiday = 0;
                                                $total_halfday = 0;
                                                $total_leave = 0;
                                            @endphp
                                            @foreach ($attendances as $values)
                                                @php
                                                    $total_attendance = 0;
                                                    $count_absent = 0;
                                                @endphp
                                                <tr>
                                                    {{-- Student Name --}}
                                                    <td>
                                                        {{ optional($values->first())->student->full_name ?? '' }}
                                                    </td>

                                                    {{-- Admission No --}}
                                                    <td>
                                                        {{ optional($values->first())->student->admission_no ?? '' }}
                                                    </td>

                                                    {{-- Present --}}
                                                    <td>
                                                        @php
                                                            $p = $values->where('attendance_type', 'P')->count();
                                                            $total_attendance += $p;
                                                            $total_grand_present += $p;
                                                        @endphp
                                                        {{ $p }}
                                                    </td>

                                                    {{-- Late --}}
                                                    <td>
                                                        @php
                                                            $l = $values->where('attendance_type', 'L')->count();
                                                            $total_attendance += $l;
                                                            $total_late += $l;
                                                        @endphp
                                                        {{ $l }}
                                                    </td>

                                                    {{-- Absent --}}
                                                    <td>
                                                        @php
                                                            $a = $values->where('attendance_type', 'A')->count();
                                                            $count_absent = $a;
                                                            $total_attendance += $a;
                                                            $total_absent += $a;
                                                        @endphp
                                                        {{ $a }}
                                                    </td>

                                                    {{-- Half Day --}}
                                                    <td>
                                                        @php
                                                            $f = $values->where('attendance_type', 'F')->count();
                                                            $total_attendance += $f;
                                                            $total_halfday += $f;
                                                        @endphp
                                                        {{ $f }}
                                                    </td>

                                                    {{-- Holiday --}}
                                                    <td>
                                                        @php
                                                            $h = $values->where('attendance_type', 'H')->count();
                                                            $total_attendance += $h;
                                                            $total_holiday += $h;
                                                        @endphp
                                                        {{ $h }}
                                                    </td>

                                                    {{-- Leave --}}
                                                    <td>
                                                        @php
                                                            $le = $values->where('attendance_type', 'Le')->count();
                                                            $total_leave += $le;
                                                        @endphp
                                                        {{ $le }}
                                                    </td>

                                                    {{-- Percentage --}}
                                                    <td>
                                                        @php
                                                            $total_present = $total_attendance - $count_absent;
                                                            $percentage = $total_attendance
                                                                ? number_format(($total_present / $total_attendance) * 100, 2) . '%'
                                                                : '100%';
                                                        @endphp
                                                        {{ $total_present . '/' . $total_attendance }}
                                                        <hr>
                                                        {{ $percentage }}
                                                    </td>

                                                    {{-- Per Day Attendance --}}
                                                    @for ($i = 1; $i <= $days; $i++)
                                                        @php
                                                            $date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                            $dayName = date('l', strtotime($date));
                                                            $present = 0;
                                                            $absent = 0;
                                                            $leave = 0;
                                                            $hasAttendance = false;
                                                        @endphp

                                                        @foreach ($values as $value)
                                                            @if ($value->attendance_date == $date)
                                                                @php
                                                                    $hasAttendance = true;

                                                                    if ($value->attendance_type === 'Le') {
                                                                        $leave++;
                                                                    } elseif (in_array($value->attendance_type, ['P', 'F', 'L'])) {
                                                                        $present++;
                                                                    } elseif ($value->attendance_type === 'A') {
                                                                        $absent++;
                                                                    }
                                                                @endphp
                                                            @endif
                                                        @endforeach

                                                        <td width="3%" class="{{ $i <= 18 ? 'all' : 'none' }}">
                                                            @if ($hasAttendance)
                                                                @if ($leave > 0 && $present === 0 && $absent === 0)
                                                                    Le
                                                                @else
                                                                    {{ $present }}/{{ $present + $absent }}
                                                                    <hr>
                                                                    @php
                                                                        $total = $present + $absent;
                                                                        echo $total > 0
                                                                            ? number_format(($present / $total) * 100, 2) . '%'
                                                                            : '0%';
                                                                    @endphp
                                                                @endif
                                                            @elseif (in_array($dayName, $weekendDayNames))
                                                                W
                                                            @endif
                                                        </td>
                                                    @endfor
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

@include('backEnd.partials.data_table_js')
