<div class="row row-gap-24" id="student_grid_container">
    @forelse($all_students as $student)
    @php
    $studentPhoto = $student->student_photo ? assetPath($student->student_photo) :
    assetPath('public/backEnd/assets/img/avatar.png');

    $classSec = [];
    foreach ($student->studentRecords as $record) {
    if(moduleStatusCheck('University')){
    $classSec[] = $record->unFaculty->name . '(' . $record->unDepartment->name . ')';
    } elseif(shiftEnable()){
    $classSec[] = $record->class->class_name . '(' . $record->section->section_name . ')[ '.$record?->shift?->name.' ]';
    } else {
    $classSec[] = $record->class->class_name . '(' . $record->section->section_name . ')';
    }
    }
    @endphp

    <div class="col-lxx-3 col-xxl-4 col-xl-6 col-sm-6">
        <div class="student_list_grid_item">
            <div class="student_list_grid_item_head">
                <div class="student_list_grid_item_id">#{{ $student->admission_no }}</div>
                <div class="student_list_grid_item_photo">
                    <img src="{{$studentPhoto}}" class="student_list_grid_item_photo_img" alt="student photo" />
                    <img class="student_list_grid_item_photo_bg"
                        src="{{assetPath('public/backEnd/assets/img/photo_bg.svg')}}" alt="photo bg" />
                </div>
                <div class="student_list_grid_item_action">
                    <div class="dropdown">
                        <a class="dropdown_toggler" href="#" role="button" data-toggle="dropdown">
                            <svg width="2" height="10" viewBox="0 0 2 10">
                                <rect width="2" height="2" rx="1" fill="#1A2945" />
                                <rect y="4" width="2" height="2" rx="1" fill="#1A2945" />
                                <rect y="8" width="2" height="2" rx="1" fill="#1A2945" />
                            </svg>
                        </a>
                        <ul class="dropdown-menu">
                            @if(userPermission('student.assign-class'))
                            <li><a class="dropdown-item"
                                    href="{{ route('student.assign-class', [$student->id]) }}">Assign Class</a></li>
                            @endif
                            @if(userPermission('student_view'))
                            <li><a class="dropdown-item" target="_blank"
                                    href="{{ route('student_view', [$student->id]) }}">View</a></li>
                            @endif
                            @if(userPermission('student_edit'))
                            <li><a class="dropdown-item" href="{{ route('student_edit', [$student->id]) }}">Edit</a>
                            </li>
                            @endif
                            @if(userPermission('disabled_student'))
                            <li><a class="dropdown-item" href="#" onclick="deleteId({{ $student->id }});"
                                    data-toggle="modal" data-target="#deleteStudentModal">Disable</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <div class="student_list_grid_item_body">
                <a href="{{ route('student_view', [$student->id]) }}" target="_blank"
                    class="student_list_grid_item_name d-block">
                    {{ $student->first_name.' '.$student->last_name }}
                </a>

                <table class="student_list_grid_info">
                    <tbody>
                        @if(moduleStatusCheck('University'))
                        <tr>
                            <td>Semester</td>
                            <td>:</td>
                            <td>
                                @php
                                $semesterLabels = [];
                                foreach ($student->studentRecords as $record) {
                                $semesterLabels[] = $record->unSemesterLabel->name ?? '-';
                                }
                                @endphp
                                {!! $semesterLabels ? implode(', ', $semesterLabels) : '-' !!}
                            </td>
                        </tr>
                        @endif
                        @if (moduleStatusCheck('Branch'))
                        <tr>
                            <td>Branch</td>
                            <td>:</td>
                            <td>{{ $student->branch?->branch_name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Class</td>
                            <td>:</td>
                            <td>{!! implode(', ', $classSec) !!}</td>
                        </tr>
                        @if (!moduleStatusCheck('University') && generalSetting()->with_guardian)
                        <tr>
                            <td>Father Name</td>
                            <td>:</td>
                            <td>{{ $student->parents?->fathers_name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Gender</td>
                            <td>:</td>
                            <td>{{ $student->gender->base_setup_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Date Of Birth</td>
                            <td>:</td>
                            <td>{{ $student->date_of_birth ? dateConvert($student->date_of_birth) : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>:</td>
                            <td>{{$student->category?->category_name}}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>:</td>
                            <td>{{ $student->mobile }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @empty
    <div class="col-lg-12 text-center">
        <p>No matching records found</p>
    </div>
    @endforelse
</div>
@if($all_students->hasPages())
<div class="row mt-4">
    <div class="col-12 d-flex justify-content-center">
        {{ $all_students->appends(request()->query())->links() }}
    </div>
</div>
@endif