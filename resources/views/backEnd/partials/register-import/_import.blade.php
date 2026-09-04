<x-upload-step step="import"/>
<style>
    .table-responsive {
        min-height: 100% !important;
    }

    table#importTable th {
        white-space: nowrap;
    }

    table#importTable td input {
        min-width: 100px;
        max-width: 100%;
    }
</style>
<form action="{{$url}}" method="POST" class="csvForm">
    <input type="hidden" name="step" value="import">
    @if(isset($required_fields))
        @foreach($required_fields as $name=>$value)
            <input type="hidden" name="{{$name}}" value="{{$value}}"/>
        @endforeach
    @endif
    @csrf
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="main-title">
                <h3 class="mb-15">@lang('exam.add_marks') |
                    <small>@lang('exam.exam'): {{$exam->title}}, @lang('common.class'):
                        {{$class->class_name}}, @lang('common.section'):
                        {{$section->section_name}}@if(shiftEnable() && @$shift) , @lang('common.shift')
                        : {{$shift->name}} @endif
                </h3>
            </div>
        </div>
    </div>
    <div class="row form">
        <div class="col-md-12 table-responsive">
            <input type="hidden" name="exam_id" value="{{$exam->id}}" id="exam_submit_id">
            <input type="hidden" name="class_id" value="{{$class->id}}" id="class_submit_id">
            <input type="hidden" name="section_id" value="{{$section->id}}" id="section_submit_id">
            <input type="hidden" name="subject_id" value="{{$subject->id}}" id="subject_submit_id">
            @if(shiftEnable() && @$shift)
                <input type="hidden" name="shift_id" value="{{$shift->id}}" id="shift_submit_id">
            @endif

            <div class="d-flex align-items-center mb-3">
                <h4 class="mb-0 mr-3">
                    <span id="totalCount">0</span> items are ready to be imported
                </h4>
                <input type="text" id="searchField" placeholder="Search..." class="form-control"
                       style="max-width: 300px;">
            </div>
            <table class="table table-bordered" id="importTable">
                <thead>
                <tr>
                    <th colspan="9" class="text-center">{{$subject->subject_name}}</th>
                </tr>
                <tr>
                    <th>
                        <label class="primary_checkbox d-flex">
                            <input type="checkbox" id="checkAll" class="check_all_td check_data" checked>
                            <span class="checkmark"></span>
                        </label>
                    </th>
                    <th>@lang('student.roll_no').</th>
                    <th>@lang('common.student').</th>
                    <th>@lang('common.class_Sec').</th>
                    @foreach($expectedHeaders as $key => $value)
                        <th>{{$value}}
                            @if(in_array(convertToSnakeCase($value),$requiredHeaders))
                                <span class="text-danger">*</span>
                        @endif
                    @endforeach
                </tr>
                </thead>
                <tbody id="tableBody">
                @php
                    $i=0;
                @endphp
                @if(count($allData) > 0)
                    @foreach($allData as  $key=>$data)
                        @php
                            $admissionColumn = $mappedHeaders->admission_no ?? 'Admission No';
                            $admissionNo = $data[$admissionColumn] ?? null;
                            $student = App\Models\SmStudent::where('admission_no', $admissionNo)->first();
                        @endphp
                        <tr>
                            <td>
                                <label class="primary_checkbox d-flex">
                                    <input type="checkbox" name="index[{{$i}}]" value="{{$key}}"
                                           class="row-check check_data"
                                           checked>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>
                                {{ $student ? $student->roll_no : '-' }}
                            </td>
                            <td>
                                {{ $student ? $student->full_name : 'Not Found' }}
                            </td>
                            <td> @if($student)
                                    {{$class->class_name}} ({{$section->section_name}})
                                @endif</td>
                            {{-- @foreach($expectedHeaders as $key => $label)
                                @php
                                    $name = convertToSnakeCase($nameHeaders[$key]);
                                    $index = convertToSnakeCase($label);
                                @endphp
                                <td>
                                    <input type="text"
                                        name="{{ $name }}[]"
                                        value="{{ $data[$mappedHeaders->$index] ?? '' }}"
                                        class="form-control input-field {{ in_array($name, $requiredHeaders) ? 'required' : '' }}">
                                </td>
                            @endforeach --}}
                            @foreach($expectedHeaders as $key => $label)
                                @php
                                    $nameKey = $nameHeaders[$key];
                                    $snakeCase = convertToSnakeCase($label);

                                    if (is_numeric($nameKey)) {
                                        $inputName = "markStore[$i][marks][$nameKey]";
                                    } elseif ($nameKey === 'Admission No') {
                                        $inputName = "markStore[$i][adimission_no]";
                                    } else {
                                        $inputName = "markStore[$i][teacher_remarks]";
                                    }
                                @endphp
                                <td>
                                    <input type="text"
                                           name="{{ $inputName }}"
                                           value="{{ $data[$mappedHeaders->$snakeCase] ?? '' }}"
                                           class="form-control input-field {{ in_array(convertToSnakeCase($nameKey), $requiredHeaders) ? 'required' : '' }}">
                                </td>
                            @endforeach
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-12 mt-20">
        <div class="submit_btn text-center">

            <button class="primary-btn semi_large2 backToMap fix-gr-bg "
                    type="button"><i class="ti-arrow-left"></i>@lang('exam.back')
            </button>

            <button class="primary-btn semi_large2 submit_button_form fix-gr-bg csvFormBtn" type="submit" disabled>
                <i class="ti-check"></i>@lang('exam.next')
            </button>
        </div>
    </div>
</form>

<script>

    $(document).ready(function () {

        const $checkAll = $('#checkAll');
        const $totalCount = $('#totalCount');
        const $rowChecks = $('.row-check');
        const $importNowBtn = $('.csvFormBtn');

        // Function to toggle row inputs based on checkbox
        function toggleRowState($checkbox) {
            let $row = $checkbox.closest('tr');
            let $inputs = $row.find('.input-field');
            if ($checkbox.is(':checked')) {
                $inputs.prop('disabled', false);
            } else {
                $inputs.prop('disabled', true);
                $inputs.removeClass('is-invalid');
            }
        }

        // Function to validate required fields and enable/disable import button
        function validateAndToggleButton() {
            let allValid = true;
            $('.input-field.required').each(function () {
                if (!$(this).is(':disabled') && $(this).val().trim()== '') {
                    $(this).addClass('is-invalid');
                    allValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            let anyRowChecked = $rowChecks.filter(':checked').length > 0;
            if (allValid && anyRowChecked) {
                $importNowBtn.prop('disabled', false).removeClass('disabled');
            } else {
                $importNowBtn.prop('disabled', true).addClass('disabled');
            }
        }

        // Event handler for checking/unchecking all rows
        $checkAll.on('change', function () {
            let isChecked = $(this).is(':checked');
            $rowChecks.prop('checked', isChecked).each(function () {
                toggleRowState($(this));
            });
            updateTotalCount();
            validateAndToggleButton();
        });

        // Event handler for individual row checkboxes
        $(document).on('change', '.row-check', function () {
            toggleRowState($(this));
            updateTotalCount();
            validateAndToggleButton();

            // Adjust "Select All" checkbox state
            let allChecked = $rowChecks.length === $rowChecks.filter(':checked').length;
            $checkAll.prop('checked', allChecked);
        });

        // Search functionality
        $('#searchField').on('keyup', function () {
            let searchValue = $(this).val().toLowerCase();
            $('#importTable #tableBody tr').each(function () {
                let rowMatch = $(this).find('td input').filter(function () {
                    return $(this).val().toLowerCase().includes(searchValue);
                }).length > 0;
                $(this).toggle(rowMatch);
            });
            updateTotalCount(); // Update count after search
            validateAndToggleButton(); // Validate fields after search
        });

        // Validate fields on input change
        $(document).on('input', '.input-field.required', function () {
            validateAndToggleButton();
        });

        // Function to update total count of selected rows
        function updateTotalCount() {
            $totalCount.text($rowChecks.filter(':checked').length);
        }

        // Initialize total count and button state
        updateTotalCount();
        validateAndToggleButton();
    });
</script>
