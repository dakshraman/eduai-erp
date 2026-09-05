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
        @include('backEnd.partials.import._upload')
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

                let formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('step', 'upload');

                @foreach($required_fields as $field)
                let {{$field}} = $('#{{$field}}').val();
                console.log({{$field}})
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
