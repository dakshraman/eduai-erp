<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{__('Class Routine')}}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{assetPath('public/backEnd/vendors/css/print/bootstrap.min.css')}}"/>
    <script type="text/javascript" src="{{assetPath('public/backEnd/vendors/js/print/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{assetPath('public/backEnd/vendors/js/print/bootstrap.min.js')}}"></script>
</head>
<style>
    @page {
        margin-top: 0px;
        margin-bottom: 0px;
    }

    table, th, tr, td {
        font-size: 11px !important;
    }
</style>
<body style="font-family: 'dejavu sans', sans-serif;" id="pdf">

<div class="container-fluid">

    <table cellspacing="0" width="100%">
        <tr>
            <td>
                <img src="{{ url('/')}}/{{@generalSetting()->logo }}" style="padding-top: 20px;" alt="">
            </td>
            <td style="text-aligh:left">
                <h3 style="font-size:20px !important; margin-bottom : 0;margin-top: 0px;"
                    class="text-white mb-0"> @lang('academics.class_routine') </h3>
                <span style="font-size:11px !important;margin-right:10px;" align="left"
                      class="text-white">@lang('common.teacher'): {{@$teacher  }} </span>
            </td>

            <td style="text-aligh:center">
                <h3 style="font-size:20px !important; margin-bottom : 0;margin-top: 0px;"
                    class="text-white mb-0"> {{isset(generalSetting()->school_name)?generalSetting()->school_name:'EduAI'}} </h3>
                <span style="font-size:11px !important;margin:0px"
                      class="text-white "> {{isset(generalSetting()->address)?generalSetting()->address:'School Address'}} </span>
            </td>
        </tr>
    </table>

    <hr style="margin-bottom: 6px;margin-top: 6px;">

    @php
        /**
         * Build routineMap[start_time][day_id] = routine
         * and collect distinct sorted time slots.
         */
        $routineMap  = [];
        $timeSlots   = [];
        $timeSlotSeen = [];

        foreach ($sm_weekends as $sm_weekend) {
            if(moduleStatusCheck('University')) {
                $dayRoutines = \App\Models\SmWeekend::unTeacherClassRoutineById($sm_weekend->id, $teacher_id);
            } else {
                $dayRoutines = \App\Models\SmWeekend::teacherClassRoutineById($sm_weekend->id, $teacher_id);
            }

            foreach ($dayRoutines as $routine) {
                $key = $routine->start_time;

                if (!isset($timeSlotSeen[$key])) {
                    $timeSlotSeen[$key] = true;
                    $timeSlots[] = [
                        'start'    => $routine->start_time,
                        'end'      => $routine->end_time,
                        'is_break' => $routine->is_break,
                    ];
                }

                if (!isset($routineMap[$key][$sm_weekend->id])) {
                    $routineMap[$key][$sm_weekend->id] = $routine;
                }
            }
        }

        usort($timeSlots, function($a, $b) {
            return strtotime($a['start']) - strtotime($b['start']);
        });
    @endphp

    <table class="table table-bordered table-striped" style="width: 100%; table-layout: fixed">
        <thead>
        <tr>
            <th style="width:12%; padding: 2px; padding-left:8px;">@lang('common.time')</th>
            @foreach($sm_weekends as $sm_weekend)
                <th style="padding: 2px; padding-left:8px;">{{@$sm_weekend->name}}</th>
            @endforeach
        </tr>
        </thead>

        <tbody>
        @forelse($timeSlots as $slot)
            <tr style="border-bottom:1px solid #000000">
                {{-- Time / period column --}}
                <td style="padding-top:2px; padding-bottom:2px; font-size:10px !important;">
                    <strong>
                        {{ date('h:i A', strtotime($slot['start'])) }}
                        &ndash;
                        {{ date('h:i A', strtotime($slot['end'])) }}
                    </strong>
                    @if($slot['is_break'])
                        <br><em>@lang('common.break')</em>
                    @endif
                </td>

                @foreach($sm_weekends as $sm_weekend)
                    <td style="padding-top:2px; padding-bottom:2px;">
                        @if($sm_weekend->is_weekend == 1)
                            <span>@lang('academics.weekend')</span>
                        @else
                            @php
                                $routine = $routineMap[$slot['start']][$sm_weekend->id] ?? null;
                            @endphp
                            @if($routine)
                                @if($routine->is_break)
                                    <strong>@lang('common.break')</strong>
                                @else
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
                                        @if($subjectCode) ({{ $subjectCode }}) @endif
                                        <br>
                                    @endif
                                    @if($routine->class)
                                        {{ $routine->class->class_name }}
                                        @if($routine->section) ({{ $routine->section->section_name }}) @endif
                                        <br>
                                    @endif
                                    @if($routine->classRoom)
                                        @lang('common.room'): {{ $routine->classRoom->room_no }}
                                    @endif
                                @endif
                            @else
                                N/A
                            @endif
                        @endif
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ $sm_weekends->count() + 1 }}">@lang('common.no_data_found')</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>


</body>

<script src="{{ assetPath('public/vendor/spondonit/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ assetPath('public/backEnd/js/pdf/html2pdf.bundle.min.js') }}"></script>
<script src="{{ assetPath('public/backEnd/js/pdf/html2canvas.min.js') }}"></script>

<script>
    function generatePDF() {
        const element = document.getElementById('pdf');
        var opt = {
            margin: 0.5,
            pagebreak: {mode: ['avoid-all', 'css', 'legacy'], before: '#page2el'},
            filename: 'teacher-class-routine.pdf',
            image: {type: 'jpeg', quality: 100},
            html2canvas: {scale: 5},
            jsPDF: {unit: 'in', format: 'a4', orientation: 'landscape'}
        };

        html2pdf().set(opt).from(element).save().then(function () {
            // window.close()
        });
    }


    $(document).ready(function () {
        @if($print)
        window.print();
        setTimeout(function () {
            window.close()
        }, 3000);
        @else
        generatePDF();
        @endif

    })
</script>
</html>


