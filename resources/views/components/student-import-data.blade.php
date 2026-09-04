@props([
    'col_size'=>12,
    'url'=>'',
    'title'=>'',
    'step'=>'import',
    'sample'=>'',
    'instructions'=>[],
    'required_fields'=>[],

])
<div>

    <div class="step-upload">
        @include('backEnd.partials.student-import._upload')
    </div>
    <div class="step-map d-none">
    </div>
    <div class="step-import d-none">
    </div>
    @push("scripts")
        <script type="text/javascript">
            let mapStepEle = $('.step-map');
            let uploadStepEle = $('.step-upload');
            let importStepEle = $('.step-import');
            let hasFileChanges = false;
            let hasMapChanges = false;

            function showLoader() {
                $('#pre-loader').removeClass('d-none');

            }
            function hideLoader() {
                $('#pre-loader').addClass('d-none');

            }

            $('#import_file').on('change', function (event) {
                hasFileChanges = true;
            });

            $(document).on('click', '.backToUpload', function (e) {
                e.preventDefault();
                uploadStepEle.removeClass('d-none');
                mapStepEle.addClass('d-none');
                importStepEle.addClass('d-none');
            });
            $(document).on('click', '.backToMap', function (e) {
                e.preventDefault();
                uploadStepEle.addClass('d-none');
                mapStepEle.removeClass('d-none');
                importStepEle.addClass('d-none');
            });
            $(document).on('click', '.uploadSubmitBtn', function (e) {
                e.preventDefault();
                let fileInput = $('#import_file')[0];

                if (fileInput.files.length === 0) {
                    toastr.error("File is required", "Error");
                    return false;
                }

                @if (moduleStatusCheck('Branch'))
                let branch_id = $('#select_branch').val();
                if (!branch_id) {
                    toastr.error("Branch is required", "Error");
                    return false;
                }
                @endif

                @if (moduleStatusCheck('University'))
                    let session_id = $('#select_session').val();
                    let faculty_id = $('#select_faculty').val();
                    let dept_id = $('#select_dept').val();
                    let academic_id = $('#select_academic').val();
                    let semester_id = $('#select_semester').val();
                    let semester_label_id = $('#select_semester_label').val();
                    let section_id = $('#select_section').val();

                    if (!session_id) {
                        toastr.error("Session is required", "Error");
                        return false;
                    }
                    if (!dept_id) {
                        toastr.error("Department is required", "Error");
                        return false;
                    }
                    if (!academic_id) {
                        toastr.error("Academic is required", "Error");
                        return false;
                    }
                    if (!semester_id) {
                        toastr.error("Semester is required", "Error");
                        return false;
                    }
                    if (!semester_label_id) {
                        toastr.error("Semester Level is required", "Error");
                        return false;
                    }
                @else
                    let session_id = $('#common_academic_years').val();
                    @if(shiftEnable())
                        let shift_id = $('#common_select_shifts').val();
                    @endif
                    let class_id = $('#common_select_classes').val();
                    let section_id = $('#common_select_sections').val();

                    if (!session_id) {
                        toastr.error("Session is required", "Error");
                        return false;
                    }
                    if (!class_id) {
                        toastr.error("Class is required", "Error");
                        return false;
                    }
                    if (!section_id) {
                        toastr.error("Section is required", "Error");
                        return false;
                    }
                @endif

                let formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('step', 'upload');
                @if (moduleStatusCheck('Branch'))
                    formData.append('branch_id', branch_id);
                @endif
                @if (moduleStatusCheck('University'))
                    formData.append('session_id', session_id);
                    formData.append('faculty_id', faculty_id);
                    formData.append('dept_id', dept_id);
                    formData.append('academic_id', academic_id);
                    formData.append('semester_id', semester_id);
                    formData.append('semester_label_id', semester_label_id);
                    formData.append('section_id', section_id);
                @else
                    formData.append('session_id', session_id);
                    @if(shiftEnable())
                        if (shift_id !== null && shift_id ) {
                            formData.append('shift_id', shift_id);
                        }
                    @endif
                    formData.append('class_id', class_id);
                    formData.append('section_id', section_id);
                @endif

                @foreach($required_fields as $field)
                let {{$field}} = $('#{{$field}}').val();
                if ({{$field}}== '' || {{$field}} === '0') {
                    toastr.error("{{ $field }} field is required.", "error");
                    return false;
                }
                formData.append('{{$field}}', {{$field}});
                @endforeach

                if (hasFileChanges) {
                    showLoader();
                    $.ajax({
                        url: '{{$url}}',
                        type: 'POST',
                        data: formData,
                        dataType: 'html',
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            mapStepEle.html(response)
                            uploadStepEle.addClass('d-none')
                            mapStepEle.removeClass('d-none')
                            importStepEle.addClass('d-none')

                            $('.headers').each(function () {
                                toggleMatchDiv($(this));
                            });

                            $('select').niceSelect();
                            hasFileChanges = false;
                            hideLoader();
                        },
                        error: function (xhr, status, error) {
                            hideLoader();

                            toastr.error("Something Went Wrong", "error");
                        }
                    });
                } else {
                    uploadStepEle.addClass('d-none')
                    mapStepEle.removeClass('d-none')
                    importStepEle.addClass('d-none')
                }


            });

            $(document).on('click', '.mapSubmitBtn', function (e) {
                e.preventDefault();
                let fileInput = $('#import_file')[0];


                if (fileInput.files.length === 0) {
                    toastr.error("File is required", "error");
                    return;
                }
                @if (moduleStatusCheck('Branch'))
                let branch_id = $('#select_import_branch').val();
                @endif
                @if (moduleStatusCheck('University'))
                    let session_id = $('#select_import_session').val();
                    let faculty_id = $('#select_import_faculty').val();
                    let dept_id = $('#select_import_dept').val();
                    let academic_id = $('#select_import_academic').val();
                    let semester_id = $('#select_import_semester').val();
                    let semester_label_id = $('#select_import_semester_label').val();
                    let section_id = $('#select_import_section').val();
                @else
                    let session_id = $('#session_import_id').val();
                    @if(shiftEnable())
                        let shift_id = $('#shift_import_id').val();
                    @endif
                    let class_id = $('#class_import_id').val();
                    let section_id = $('#section_import_id').val();
                @endif

                let dropdowns = $('.primary_select.headers');

                let selectedHeaders = {};
                dropdowns.each(function () {
                    let name = $(this).data('name');
                    let value = $(this).val();
                    if (name) {
                        selectedHeaders[name] = value;
                    }
                });

                let formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('step', 'map');
                formData.append('headers', JSON.stringify(selectedHeaders));
                @if (moduleStatusCheck('Branch'))
                formData.append('branch_id', branch_id);
                @endif
                @if (moduleStatusCheck('University'))
                    formData.append('session_id', session_id);
                    formData.append('faculty_id', faculty_id);
                    formData.append('dept_id', dept_id);
                    formData.append('academic_id', academic_id);
                    formData.append('semester_id', semester_id);
                    formData.append('semester_label_id', semester_label_id);
                    formData.append('section_id', section_id);
                @else
                    formData.append('session_id', session_id);
                    @if(shiftEnable())
                        if (shift_id !== null && shift_id ) {
                            formData.append('shift_id', shift_id);
                        }
                    @endif
                    formData.append('class_id', class_id);
                    formData.append('section_id', section_id);
                @endif
                
                @foreach($required_fields as $field)
                let {{$field}} = $('#{{$field}}').val();
                console.log({{$field}})
                if ({{$field}}== '' || {{$field}} === '0') {
                    toastr.error("File is required", "error");
                    return false;
                }
                formData.append('{{$field}}', {{$field}});
                @endforeach

                if (hasMapChanges) {
                    showLoader();
                    $.ajax({
                        url: '{{$url}}',
                        type: 'POST',
                        data: formData,
                        dataType: 'html',
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            hasMapChanges = false;
                            importStepEle.html(response)
                            uploadStepEle.addClass('d-none');
                            mapStepEle.addClass('d-none');
                            importStepEle.removeClass('d-none');
                            hideLoader();
                        },
                        error: function () {
                            toastr.error("Something went wrong", "error");
                            hideLoader();
                        }
                    });
                } else {
                    uploadStepEle.addClass('d-none');
                    mapStepEle.addClass('d-none');
                    importStepEle.removeClass('d-none');
                }


            });
            $(document).on('click', '.csvFormBtn', function (e) {
                showLoader();
            });

            function toggleMatchDiv(selectElement) {
                let selectedValue = selectElement.val(); // Get the selected value
                let hasMatchDiv = selectElement.closest('tr').find('.hasMatch');
                let notMatchDiv = selectElement.closest('tr').find('.notMatch');

                // Toggle visibility based on whether a value is selected
                selectedValue ? hasMatchDiv.show() && notMatchDiv.hide() : notMatchDiv.show() && hasMatchDiv.hide();
                hasMapChanges = true;
            }

            // Event listener to toggle divs on selection change
            $(document).on('change', '.headers', function () {
                toggleMatchDiv($(this));
            });


        </script>

    @endpush

</div>