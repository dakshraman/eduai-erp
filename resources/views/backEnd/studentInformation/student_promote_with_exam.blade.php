@extends('backEnd.master')
@section('title') 
@lang('student.student_promote')
@endsection

@section('mainContent')
<section class="sms-breadcrumb mb-20 up_breadcrumb">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('student.student_promote')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('student.student_information')</a>
                <a href="#">@lang('student.student_promote')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('common.select_criteria') </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        {{ html()->form('GET', route('student-current-search-with-exam'))->attributes([
                            'class' => 'form-horizontal',
                            'files' => true,
                            'enctype' => 'multipart/form-data',
                            'id' => 'search_promoteA',
                        ])->open() }}
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="primary_input_label" for="">@lang('common.select_academic_year') <span class="text-danger">*</span></label>
                                    <select class="primary_select  form-control{{ $errors->has('current_session') ? ' is-invalid' : '' }}" name="current_session" id="common_academic_years">
                                        <option data-display="@lang('common.select_academic_year') *" value="">@lang('common.select_academic_year') *</option>
                                        @foreach($sessions as $session)
                                        <option value="{{$session->id}}" {{isset($current_session)? ($current_session == $session->id? 'selected':''):''}}>{{$session->year}} [{{$session->title}}]</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('current_session'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('current_session') }}
                                    </span>
                                    @endif                                  
                                </div>
                                <div class="col-lg-3">
                                    <label class="primary_input_label" for="">@lang('student.promote_academic_year') <span class="text-danger">*</span></label>
                                    <select class="primary_select  form-control{{ $errors->has('promote_session') ? ' is-invalid' : '' }}" name="promote_session" id="promote_session">
                                        <option data-display="@lang('student.promote_academic_year') *" value="">@lang('student.promote_academic_year') *</option>
                                        @foreach($sessions as $session)
                                        <option value="{{$session->id}}" {{isset($promote_session)? ($promote_session == $session->id? 'selected':''):''}}>{{$session->year}} [{{$session->title}}]</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('promote_session'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('promote_session') }}
                                    </span>
                                    @endif                                  
                                </div>
                                @if (moduleStatusCheck('Branch'))
                                    @include('branch::components.branch_select', [
                                        'grid_class' => 'col-lg-3',
                                        'mb' => 'mb-15',
                                        'branch_id' => isset($branch_id) ? $branch_id : '',
                                    ])
                                @endif
                                @include('backEnd.common.search_criteria', [
                                    'div' => shiftEnable() ? 'col-lg-3' : 'col-lg-3',
                                    'mt' => 'mt-30-md',
                                    'visiable' => ['shift', 'class', 'section'],
                                    'required' => ['class', 'section'],
                                    'class_name' => 'current_class',
                                    'section_name' => 'current_section',
                                    'selected' => [
                                        'academic_year' => @$current_session,
                                        'class_id' => @$current_class,
                                        'section_id' => @$current_section,
                                        'shift_id' => @$current_shift,
                                    ],
                                ])
                                {{-- <div class="col-lg-3 mt-30-md">
                                    <select class="primary_select  form-control{{ $errors->has('current_class') ? ' is-invalid' : '' }}" id="classSelectStudent" name="current_class">
                                        <option data-display="@lang('student.select_current_class') *" value="">@lang('student.select_current_class') *</option>
                                        @foreach($classes as $class)
                                        <option value="{{$class->id}}" {{isset($current_class)? ($current_class == $class->id? 'selected':''):''}}>{{$class->class_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="pull-right loader loader_style" id="select_class_loader">
                                        <img class="loader_img_style" src="{{assetPath('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                                    </div>
                                     @if ($errors->has('current_class'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('current_class') }}
                                    </span>
                                    @endif 
                                </div>
                                <div class="col-lg-3 mt-30-md" id="sectionStudentDiv">
                                    <select class="primary_select  form-control{{ $errors->has('section') ? ' is-invalid' : '' }}" id="sectionSelectStudent"  name="current_section">
                                        <option data-display="@lang('common.select_section')" value="">@lang('common.select_section')</option>
                                       @isset($sections) 
                                        @foreach($sections as $section)
                                         <option  value="{{$section->sectionName !='' ?  $section->sectionName->id : ''}}" {{isset($current_section)? ($current_section == ($section->sectionName !='' ? $section->sectionName->id :'')? 'selected':''):''}} >{{$section->sectionName->section_name}}</option>
                                         @endforeach
                                       @endisset
                                    </select>
                                    <div class="pull-right loader loader_style" id="select_section_loader">
                                        <img class="loader_img_style" src="{{assetPath('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                                    </div>
                                    @if ($errors->has('section'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('section') }}
                                    </span>
                                    @endif
                                </div> --}}

                                <div class="col-lg-3 mt-30-md">
                                    <label class="primary_input_label" for="">@lang('common.exam') <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control{{ $errors->has('exam') ? ' is-invalid' : '' }}" name="exam">
                                        <option data-display="@lang('exam.select_exam')*" value="">@lang('exam.select_exam') *</option>
                                        @foreach($exams as $exam)
                                            <option value="{{$exam->id}}" {{isset($exam_id)? ($exam_id == $exam->id? 'selected':''):''}}>{{$exam->title}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('exam'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('exam') }}
                                    </span>
                                    @endif
                                </div>
                               
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg" id="search_promote">
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


    @if(isset($students))
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-30">@lang('student.promote') | 
                                    <small>
                                        @lang('common.academic_year') : {{ $search_current_academic_year !='' ? $search_current_academic_year->year .'['. $search_current_academic_year->title .']' :'' }},
                                        @if (moduleStatusCheck('Branch'))
                                        @lang('common.branch'): {{$search_current_branch != '' ? $search_current_branch->branch_name :' '}}, 
                                        @endif
                                        @lang('common.class'): {{$search_current_class != '' ? $search_current_class->class_name :' '}}, 
                                        @lang('common.section'): {{$search_current_section !='' ? $search_current_section->section_name : ' '}},
                                        @if(shiftEnable())
                                        @lang('common.shift'): {{$search_current_shift !='' ? $search_current_shift->name : ' '}},
                                        @endif
                                        @lang('exam.exam'): {{ $search_exams !='' ? $search_exams : ' '}},
                                        
                                        <strong> @lang('student.promote_academic_year') </strong>: {{ $search_promote_academic_year !='' ? $search_promote_academic_year->year .'['. $search_promote_academic_year->title .']' :''}} 
                                    </small>
                                </h3>
                            </div>
                            @if ($errors->any())
                                <div >
                                    <div class="text-danger">{{ __('common.whoops_something_went_wrong') }}</div>
                                    <ul class="mt-1 text-danger">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{ html()->form('POST', route('student-promote-store'))->attributes([
                        'class' => 'form-horizontal',
                        'files' => true,
                        'enctype' => 'multipart/form-data',
                        'id' => 'student_promote_submit',
                    ])->open() }}
                    <input type="hidden" name="current_session" value="{{$current_session}}">
                    <input type="hidden" name="pre_class" value="{{$current_class}}">
                    <input type="hidden" name="pre_section" value="{{$current_section}}">
                    <input type="hidden" name="promote_session" value="{{$promote_session}}">
                    @if (moduleStatusCheck('Branch'))
                    <input type="hidden" name="branch_id" value="{{$branch_id}}">
                    @endif
                    <input type="hidden" name="pre_shift" value="{{$current_shift}}">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th width="10%">
                                       
                                            <input type="checkbox" id="checkAll" class="common-checkbox" name="checkAll">
                                            <label for="checkAll">@lang('common.all')</label>
                                        </th>                                       
                                        <th>@lang('student.current_roll')</th>
                                        <th>@lang('common.name')</th>
                                        <th>@lang('exam.total_marks')</th>
                                        <th>@lang('exam.gpa')</th>
                                        <th>@lang('reports.result')</th>
                                        <th>@lang('exam.position')</th>
                                        <th>@if(shiftEnable()) @lang('admin.class_Sec_shift') @else @lang('common.class_section') @endif</th>                                                                        
                                                                            
                                        <th>@lang('student.promote_class')</th>           
                                        <th>@lang('student.promote_section')</th>  
                                        @if(shiftEnable())         
                                        <th>@lang('student.promote_shift')</th>   
                                        @endif                       
                                        <th>@lang('student.next_roll_number')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($students  as $key=>$student)
                                  <input type="hidden" name="promote[{{$student->studentinfo->id}}][result]"  value="{{ $student->result ?? 'F' }}">

                                    <tr>
                                        <td>
                                            <input type="checkbox" id="student_{{$student->studentinfo->id}}" class="common-checkbox promote_check" name="promote[{{$student->studentinfo->id}}][student]" value="{{$student->studentinfo->id}}">
                                            <label for="student_{{$student->studentinfo->id}}"></label>
                                        </td>
                                        <td> <a href="{{route('student_view',[$student->studentinfo->id]) }}"  target="_blank" rel="noopener noreferrer">  <h5 style="color:#A235EC">{{$student->studentinfo->roll_no}}</h5></a> </td>
                                        <td>{{  $student->studentinfo->first_name .' '.$student->studentinfo->last_name }}</td>
                                       <td>{{ $student->total_marks}}</td>
                                       <td>{{ $student->gpa_point }}</td>
                                       <td>{{ $student->result }}</td>
                                       <td>{{ $key+1 }}</td>
                                        <td>{{$student->class !=""?$student->class->class_name:""}} ({{ $student->section !=""?$student->section->section_name:"" }}) @if(shiftEnable()) [{{ $student->shift !=""?$student->shift->name:"" }}] @endif </td>
                                       
                                        <td>
                                        <div class="row">
                                            <div class="col-lg-12">
       
                                              <select class="primary_select form-control {{ $errors->has('class') ? ' is-invalid' : '' }} promote_class" id="promote_class" name="promote[{{$student->studentinfo->id}}][class]">
                                               <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class') *</option>
                                               @foreach($classes as $class)
                                                <option value="{{ @$class->id}}"  {{ ( ($next_class && $next_class->id == $class->id) ? "selected":"")}}>{{ $class->class_name}}</option>
                                               @endforeach
                                           </select>
                                           @if ($errors->has('class'))
                                           <span class="text-danger invalid-select" role="alert">
                                               {{ $errors->first('class') }}
                                           </span>
                                           @endif
       
                                       </div>
                                       </div>
                                       </td>
                                        <td>
                                            
                                            <div class="row">

                                                    <div class="col-lg-12" id="promote_section_div">
            
                                                    <select class="primary_select form-control{{ $errors->has('section') ? ' is-invalid' : '' }} promote_section" id="promote_section"   name="promote[{{$student->studentinfo->id}}][section]">
                                                        
                                                        <option data-display="@lang('common.select_section') *" value="">@lang('common.select_section') *
                                                        </option>
                                                        @if(isset($next_sections) && $next_sections->count())
                                                            @foreach ($next_sections as $section)
                                                                <option value="{{ $section->sectionWithoutGlobal->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $section->sectionWithoutGlobal->section_name }}</option>
                                                            @endforeach
                                                        @elseif($next_class)
                                                            @foreach ($next_class->classSection as $section)
                                                                <option value="{{ $section->sectionName->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $section->sectionName->section_name }}</option>
                                                            @endforeach
                                                        @endif
                                                            </select>
                                                            <div class="pull-right loader loader_style select_section_promote" id="select_section_promote">
                                                                <img class="loader_img_style" src="{{assetPath('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                                                            </div>  
                                                            @if ($errors->has('section'))
                                                            <span class="text-danger invalid-select" role="alert">
                                                                {{ $errors->first('section') }}</span>
                                                            @endif
            
                                                    </div>
                                            </div>
                                        </td>
                                        @if(shiftEnable())
                                        <td>
                                            
                                            <div class="row">

                                                    <div class="col-lg-12">
            
                                                    <select class="primary_select form-controlpromote_shift" id="promote_shift"   name="promote[{{$student->studentinfo->id}}][shift]">
                                                        
                                                        <option data-display="@lang('common.select_shift') *" value="">@lang('common.select_shift') *
                                                        </option>
                                                        @if($next_class)
                                                            @foreach (shifts() as $shift)
                                                                <option  value="{{ $shift->id }}">{{ $shift->name }}</option>
                                                            @endforeach
                                                            
                                                        @endif
                                                    
                                                    </div>
                                            </div>
                                        </td>
                                        @endif
                                        <td> 
                                            <div class="row">
                                                <div class="col-lg-12"> 
                                                    <div class="primary_input">
                                                    <input class="primary_input_field form-control{{ @$errors->has('name') ? ' is-invalid' : '' }} promote_roll_number" type="number" name="promote[{{$student->studentinfo->id}}][roll_number]" autocomplete="off" value="{{ $student->roll_no ?? 'F' }}">
                                                      
                                                    <span class="text-danger errorExitRoll"></span>  
                                                    {{--  --}}
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                       
                                    </tr>
                                    @endforeach
                              
                                </tbody>
                            </table>
                            @if(userPermission(82))
                            <div class="col-lg-12 mt-5 text-center">
                                <button type="submit" class="primary-btn fix-gr-bg" id="student_promote_submit">
                                    <span class="ti-check"></span>
                                    @lang('student.promote')
                                </button>
                            </div>
                            @endif
                        </div>

                    </div>

                    <div class="row">
                        
                    </div>

                    {{ html()->form()->close() }}
                </div>
            </div>
    </div>
</section>
@endif
<script>



</script>

@endsection
@section('script')
<script>
    $(document).ready(function(){
        $(document).on('change', '.promote_section', function () {
            $(this).closest('tr').find('.promote_check').prop('checked',true); 
        });

          $(document).on('keyup', '.promote_roll_number', function () {
                var url          = $("#url").val();
                var class_id     =  $(this).closest('tr').find('.promote_class').val();
                var section_id   =  $(this).closest('tr').find('.promote_section').val();
                var promote_roll_number   =  $(this).closest('tr').find('.promote_roll_number').val();

              if(class_id ==''){

                 var class_error_msg='Please select Class';
                $(this).closest('tr').find('.errorExitRoll').delay(3000).fadeOut('slow').html(class_error_msg);
              
              }
               if(section_id ==''){
            
                var class_error_msg='Please select Section';
                $(this).closest('tr').find('.errorExitRoll').delay(3000).fadeOut('slow').html(class_error_msg);
                
              }

              var formData = {
                   class_id : class_id,
                   section_id : section_id,
                   promote_roll_number : promote_roll_number,
                 };

            var $this = $(this);
          
             $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "ajaxStudentRollCheck",
                                                   
             success: function(data) {                             
                  console.log(data);
              if(data > 0){
                 var error_msg='Roll Already Exit';
                 $this.closest('tr').find('.errorExitRoll').delay(5000).fadeOut('slow').html(error_msg); 
                // 
              }
                                                       
                                                   
            },
                                                
            error: function(data) {
                
            },
                                                
            });

            });
        

    })

</script>
@endsection