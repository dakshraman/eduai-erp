{{ html()->form('GET', route('student-list-search-data'))->open() }}
<div class="row">
    <div class="col-lg-12">
        <div class="white-box filter_card">
            <div class="row">
                <div class="col-lg-8 col-md-6 col-sm-6">
                    <div class="main-title mt_0_sm mt_0_md">
                        <h3 class="mb-15">@lang('common.select_criteria')</h3>
                    </div>
                </div>

                @if (userPermission('student_store'))
                    <div class="col-lg-4 col-md-6 col-sm-6 text-left text-sm-right mb-4 mb-sm-0">
                        <a href="{{ route('student_admission') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('student.add_student')
                        </a>
                    </div>
                @endif
                
            </div>
            <div class="row row-gap-24">
                @if (moduleStatusCheck('Branch'))
                    @include('branch::components.branch_select', [
                        'grid_class' => 'col-lg-4 col-md-6',
                        'mb' => '',
                        'branch_id' => isset($branch_id) ? $branch_id : old('branch_id'),
                        'input_branch_id' => 'branch_id',
                    ])
                @endif
                @if (moduleStatusCheck('University'))
                    @includeIf(
                        'university::common.session_faculty_depart_academic_semester_level',
                        ['mt' => 'mt-30', 'hide' => ['USUB'], 'required' => ['USEC']]
                    )
                    <div class="col-lg-4 col-md-6 mt-25">
                        <div class="primary_input ">
                            <input class="primary_input_field" type="text" placeholder="Name" name="name"
                                value="{{ isset($name) ? $name : '' }}">
                            <label class="primary_input_label" for="">@lang('student.search_by_name')</label>

                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mt-25">
                        <div class="primary_input md_mb_20">
                            <label class="primary_input_label" for="">@lang('student.search_by_roll_no')</label>
                            <input class="primary_input_field" type="text" placeholder="Roll" name="roll_no"
                                value="{{ isset($roll_no) ? $roll_no : '' }}">

                        </div>
                    </div>
                @else
                    @include('backEnd.shift.shift_class_section_include', [
                        'div' =>  'col-lg-4 col-md-6',
                        'mt' => 'mt-0',
                        'visiable' => ['academic_year', 'shift', 'class', 'section'],
                        'required' => ['academic_year'],
                        'title' => ['academic_year', 'class', 'section', 'shift'],
                        'class_name' => 'class_id',
                        'section_name' => 'section_id',
                        'selected' => [
                            'shift_id' => @$shift_id,
                            'class_id' => @$class_id,
                            'section_id' => @$section_id,
                        ],
                    ])
                    <div class="col-lg-4 col-md-6">
                        <div class="primary_input sm_mb_20 ">
                            <label class="primary_input_label" for="">@lang('student.search_by_name')</label>

                            <input class="primary_input_field" type="text" placeholder="Name" name="name"
                                value="{{ isset($name) ? $name : old('name') }}">

                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="primary_input sm_mb_20 ">
                            <label class="primary_input_label" for="">@lang('student.search_by_roll')</label>
                            <input class="primary_input_field" type="text" placeholder="Roll" name="roll_no"
                                value="{{ isset($roll_no) ? $roll_no : old('roll_no') }}">


                        </div>
                    </div>
                @endif
                <div class="col-lg-12 mt-20 text-right">
                    <button type="submit" class="primary-btn small fix-gr-bg" id="btnsubmit">
                        <span class="ti-search pr-2"></span>
                        @lang('common.search')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="academic_id" value="{{ @$academic_year ?? getAcademicId() }}">
<input type="hidden" id="class" value="{{ @$class_id }}">
<input type="hidden" id="section" value="{{ @$section_id }}">
<input type="hidden" id="shift_id" value="{{ @$shift_id }}">
<input type="hidden" id="roll" value="{{ @$roll_no }}">
<input type="hidden" id="name" value="{{ @$name }}">
<input type="hidden" id="un_session" value="{{ @$data['un_session_id'] }}">
<input type="hidden" id="un_academic" value="{{ @$data['un_academic_id'] }}">
<input type="hidden" id="un_faculty" value="{{ @$data['un_faculty_id'] }}">
<input type="hidden" id="un_department" value="{{ @$data['un_department_id'] }}">
<input type="hidden" id="un_semester_label" value="{{ @$data['un_semester_label_id'] }}">
<input type="hidden" id="un_section" value="{{ @$data['un_section_id'] }}">
{{ html()->form()->close() }}
