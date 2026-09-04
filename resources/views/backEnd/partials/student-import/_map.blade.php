<x-upload-step step="map"/>
<style>
.table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #dee2e6;
    font-size: 15px;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.table thead th {
    background-color: #f4f6f9;
    padding: 12px 16px;
    font-weight: 600;
    color: #333;
    border-bottom: 1px solid #e0e0e0;
    text-align: left;
}

.table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f1f1;
    vertical-align: middle;
}

/* Select field */
select.primary_select.headers {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #fff;
    font-size: 14px;
    color: #333;
    transition: border 0.3s ease;
}

select.primary_select.headers:focus {
    border-color: #3498db;
    outline: none;
}

/* Status Icons */
.hasMatch, .notMatch {
    display: none;
    font-size: 18px;
}

.hasMatch i {
    color: #28a745;
}

.notMatch i {
    color: #dc3545;
}

/* Show correct icon dynamically (handled via JS if needed) */
tr.matched .hasMatch {
    display: inline-block;
}
tr.unmatched .notMatch {
    display: inline-block;
}

/* Optional: Zebra striping */
.table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>

<form action="#">
    <input type="hidden" name="step" value="map">

    @csrf
    @if(moduleStatusCheck('Branch'))
    <input type="hidden" value="{{$branch_id}}" id="select_import_branch">
    @endif
    @if(moduleStatusCheck('University'))
    <input type="hidden" value="{{$session_id}}" id="select_import_session">
    <input type="hidden" value="{{$faculty_id}}" id="select_import_faculty">
    <input type="hidden" value="{{$dept_id}}" id="select_import_dept">
    <input type="hidden" value="{{$academic_id}}" id="select_import_academic">
    <input type="hidden" value="{{$semester_id}}" id="select_import_semester">
    <input type="hidden" value="{{$semester_label_id}}" id="select_import_semester_label">
    <input type="hidden" value="{{$section_id}}" id="select_import_section">
    @else
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="main-title">
                <h3 class="mb-15">@lang('student.student_import') |
                    <small>@if(moduleStatusCheck('Branch')) @lang('common.branch'): {{$branch->branch_name}}, @endif @lang('common.academic_year'): {{$session->title}}, @lang('common.class'):
                        {{$class->class_name}}, @lang('common.section'):
                        {{$section->section_name}} @if(shiftEnable() && @$shift) , @lang('common.shift'): {{$shift->name}} @endif
                </h3>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{$session->id}}" id="session_import_id">
    <input type="hidden" value="{{$class->id}}" id="class_import_id">
    <input type="hidden" value="{{$section->id}}" id="section_import_id">
    @if(shiftEnable() && @$shift)
        <input type="hidden" value="{{$shift->id}}" id="shift_import_id">
    @endif
    @endif
    <div class="row form">
        <div class="col-md-12">
            <table class="table table-bordered ">
                <thead>
                <tr>
                    <th>@lang('exam.filed')</th>
                    <th>@lang('exam.your_filed')</th>
                    <th>@lang('exam.status')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($expectedHeaders as $key => $value)
                    <tr>
                        <td>{{$value}}</td>
                        <td>
                            <select class="primary_select headers "
                                    name="headers[{{convertToSnakeCase($value)}}]"
                                    data-name="{{convertToSnakeCase($value)}}"
                            >
                                <option value="">@lang('exam.not_matched')</option>
                                @foreach($filteredHeaders as $key2 => $newValue)
                                    <option value="{{$newValue}}"
                                        {{convertToSnakeCase($value)==$newValue?'selected':''}}
                                    >
                                        {{convertToTitleCase($newValue)}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="hasMatch">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="notMatch">
                                <i class="fas fa-times-circle text-danger"></i>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12 mt-20">
        <div class="submit_btn text-center ">

            <button class="primary-btn semi_large2 backToUpload fix-gr-bg "
                    type="button"><i class="ti-arrow-left"></i>@lang('exam.back')
            </button>

            <button class="primary-btn semi_large2 mapSubmitBtn fix-gr-bg "
                    type="button"><i class="ti-arrow-right"></i>@lang('exam.next')
            </button>
         </div>
    </div>
</form>
