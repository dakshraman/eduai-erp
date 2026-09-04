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
            font-size: 11px;
            vertical-align: middle;
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
<div role="tabpanel" class="tab-pane fade" id="subjectAttendance">
    <div>
        @if (isset($subjectAttendance))
            <section class="student-attendance">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title mb-0">
                                <h3 class="mb-15">@lang('student.student_attendance_report')
                                    <small>
                                        <span class="text-success">P:<span id="total_present"></span></span>
                                        <span class="text-warning">L:<span id="total_late"></span></span>
                                        <span class="text-danger">A:<span id="total_absent"></span></span>
                                        <span class="text-info">F:<span id="total_halfday"></span></span>
                                        <span class="text-dark">H:<span id="total_holiday"></span></span>
                                        <span class="text-muted">Le:<span id="total_leave"></span></span>
                                    </small>
                                </h3>
                            </div>
                        </div>
                        {{-- <div class="col-lg-6 no-gutters mb-30">
                            @if (userPermission(536))
                                <a href="{{ route('subject-attendance/print', [$class_id, $section_id, $month, $year]) }}"
                                    class="primary-btn small fix-gr-bg pull-right" target="_blank"><i
                                        class="ti-printer">
                                    </i>@lang('common.print')</a>
                            @endif
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="lateday d-flex flex-wrap">
                                <div class="mr-3 mb-10">@lang('student.present'): <span class="text-success">P</span></div>
                                <div class="mr-3 mb-10">@lang('student.late'): <span class="text-warning">L</span></div>
                                <div class="mr-3 mb-10">@lang('student.absent'): <span class="text-danger">A</span></div>
                                <div class="mr-3 mb-10">@lang('student.half_day'): <span class="text-info">F</span></div>
                                <div class="mr-3 mb-10">@lang('student.holiday'): <span class="text-dark">H</span></div>
                                <div class="mr-3 mb-10">@lang('student.leave'): <span class="text-muted">Le</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $weekendDayNames = getWeekendDays(Auth::user()->school_id);
                            @endphp
                            <div class="table-responsive">
                                <table id="table_id1" class="table table-responsive" cellspacing="0" width="100%">
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
                                                        $date = $year . '-' . $month . '-' . $i;
                                                        $day = date('D', strtotime($date));
                                                        echo $day;
                                                    @endphp
                                                </th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $p = $l = $a = $f = $h = $le = 0;
                                            $total_attendance = 0;
                                            $count_absent = 0;
                                        @endphp
                                        <tr>
                                            <td>{{ @$student_detail->full_name }}</td>
                                            <td>{{ @$student_detail->admission_no }}</td>

                                            {{-- P --}}
                                            <td>
                                                @foreach ($subjectAttendance as $value)
                                                    @if ($value->attendance_type == 'P')
                                                        @php $p++; $total_attendance++; @endphp
                                                    @endif
                                                @endforeach
                                                {{ $p }}
                                            </td>

                                            {{-- L --}}
                                            <td>
                                                @foreach ($subjectAttendance as $value)
                                                    @if ($value->attendance_type == 'L')
                                                        @php $l++; $total_attendance++; @endphp
                                                    @endif
                                                @endforeach
                                                {{ $l }}
                                            </td>

                                            {{-- A --}}
                                            <td>
                                                @foreach ($subjectAttendance as $value)
                                                    @if ($value->attendance_type == 'A')
                                                        @php $a++; $total_attendance++; $count_absent++; @endphp
                                                    @endif
                                                @endforeach
                                                {{ $a }}
                                            </td>

                                            {{-- F --}}
                                            <td>
                                                @foreach ($subjectAttendance as $value)
                                                    @if ($value->attendance_type == 'F')
                                                        @php $f++; $total_attendance++; @endphp
                                                    @endif
                                                @endforeach
                                                {{ $f }}
                                            </td>

                                            {{-- H --}}
                                            <td>
                                                @foreach ($subjectAttendance as $value)
                                                    @if ($value->attendance_type == 'H')
                                                        @php $h++; $total_attendance++; @endphp
                                                    @endif
                                                @endforeach
                                                {{ $h }}
                                            </td>

                                            {{-- Le --}}
                                            <td>
                                                @foreach ($subjectAttendance as $value)
                                                    @if ($value->attendance_type == 'Le')
                                                        @php $le++; @endphp
                                                    @endif
                                                @endforeach
                                                {{ $le }}
                                            </td>

                                            {{-- % --}}
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

                                            {{-- Daily Breakdown --}}
                                            @for ($i = 1; $i <= $days; $i++)
                                                @php
                                                    $date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                    $dayName = date('l', strtotime($date));
                                                    $present = 0;
                                                    $absent = 0;
                                                    $leave = 0;
                                                    $hasAttendance = false;
                                                @endphp

                                                @foreach ($subjectAttendance as $value)
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
</div>
