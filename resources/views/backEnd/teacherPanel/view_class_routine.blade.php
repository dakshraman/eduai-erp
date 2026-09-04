@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-20">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('academics.class_routine')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('academics.academics')</a>
                <a href="#">@lang('academics.class_routine')</a>
            </div>
        </div>
    </div>
</section>

@if(isset($sm_weekends))
<section class="mt-20">
    <div class="container-fluid p-0">
        <div class="row mt-40">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-30">@lang('academics.class_routine')</h3>
                </div>
            </div>
            <div class="col-lg-8 pull-right mb-30">
                <a href="{{route('print-teacher-routine', [$teacher_id])}}" class="primary-btn small fix-gr-bg pull-right" target="_blank">
                    <i class="ti-printer"> </i> @lang('common.print')
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                @php
                    /**
                     * Build a lookup: $routineMap[start_time][day_id] = routine object
                     * Also collect all distinct time slots ordered by start_time.
                     */
                    $routineMap  = [];
                    $timeSlots   = []; // [ ['start'=>..., 'end'=>..., 'is_break'=>...], ... ]
                    $timeSlotKeys = []; // keyed by "start_time" string for dedup

                    foreach ($sm_weekends as $sm_weekend) {
                        foreach ($sm_weekend->teacherClassRoutine as $routine) {
                            $key = $routine->start_time;

                            // collect distinct time slot info
                            if (!isset($timeSlotKeys[$key])) {
                                $timeSlotKeys[$key] = true;
                                $timeSlots[] = [
                                    'start'    => $routine->start_time,
                                    'end'      => $routine->end_time,
                                    'is_break' => $routine->is_break,
                                ];
                            }

                            // store only one routine per (time, day) — take first found
                            if (!isset($routineMap[$key][$sm_weekend->id])) {
                                $routineMap[$key][$sm_weekend->id] = $routine;
                            }
                        }
                    }

                    // Sort time slots by start_time
                    usort($timeSlots, function($a, $b) {
                        return strtotime($a['start']) - strtotime($b['start']);
                    });
                @endphp

                @if(count($timeSlots) > 0)
                <table class="table school-table-style" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('academics.period') / @lang('common.time')</th>
                            @foreach($sm_weekends as $sm_weekend)
                                <th>{{ $sm_weekend->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slot)
                            <tr>
                                {{-- Time column --}}
                                <td>
                                    <strong>
                                        {{ date('h:i A', strtotime($slot['start'])) }}
                                        &ndash;
                                        {{ date('h:i A', strtotime($slot['end'])) }}
                                    </strong>
                                    @if($slot['is_break'])
                                        <br><span class="text-muted">@lang('common.break')</span>
                                    @endif
                                </td>

                                {{-- One column per day --}}
                                @foreach($sm_weekends as $sm_weekend)
                                    <td>
                                        @if($sm_weekend->is_weekend == 1)
                                            <span class="text-muted">@lang('academics.weekend')</span>
                                        @else
                                            @php
                                                $routine = $routineMap[$slot['start']][$sm_weekend->id] ?? null;
                                            @endphp

                                            @if($routine)
                                                @if($routine->is_break)
                                                    <strong>@lang('common.break')</strong>
                                                @else
                                                    {{-- Subject --}}
                                                    @php
                                                        if(moduleStatusCheck('University')) {
                                                            $subjectName = $routine->unSubject ? $routine->unSubject->subject_name : '';
                                                            $subjectCode = $routine->unSubject ? $routine->unSubject->subject_code : '';
                                                        } else {
                                                            $subjectName = $routine->subject ? $routine->subject->subject_name : '';
                                                            $subjectCode = $routine->subject ? $routine->subject->subject_code : '';
                                                        }
                                                    @endphp
                                                    @if($subjectName)
                                                        <strong>{{ $subjectName }}</strong>
                                                        @if($subjectCode)
                                                            <small>({{ $subjectCode }})</small>
                                                        @endif
                                                        <br>
                                                    @endif

                                                    {{-- Class & Section --}}
                                                    @if($routine->class)
                                                        <span>{{ $routine->class->class_name }}</span>
                                                        @if($routine->section)
                                                            <span>({{ $routine->section->section_name }})</span>
                                                        @endif
                                                        <br>
                                                    @endif

                                                    {{-- Room --}}
                                                    @if($routine->classRoom)
                                                        <small><strong>@lang('common.room'):</strong> {{ $routine->classRoom->room_no }}</small>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <div class="alert alert-info">@lang('common.no_data_found')</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

@endsection
