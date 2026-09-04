{{ html()->form('POST', route('lead.update'))->attributes(['class' => 'form-horizontal', 'files' => true, 'enctype' => 'multipart/form-data', 'id' => 'updateLeadForm'])->open() }}
<input type="hidden" value="{{ $editLead->id }}" name="id">
<div class="modal-body" id="editLeadModal">
    <div class="container-fluid">
        <form action="">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">

                        <div class="col-lg-4">
                            <label class="primary_input_label" for="">@lang('admin.source') </label>
                            <select class="primary_select" name="source_id">
                                <option data-display="Source *" value="">@lang('admin.source')*</option>
                                @foreach ($sources as $source)
                                    <option value="{{ @$source->id }}"
                                        {{ $source->id == $editLead->source_id ? 'selected' : '' }}>
                                        {{ @$source->source_name }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($errors->has('source_id'))
                                <span class="text-danger invalid-select" role="alert" style="display: block">
                                    {{ $errors->first('source_id') }}
                                </span>
                            @endif


                        </div>
                        <div class="col-lg-4">
                            <label class="primary_input_label" for="">@lang('lead::lead.status') </label>
                            <select class="primary_select" name="status_id">
                                <option data-display="@lang('lead::lead.status') *" value="">
                                    @lang('lead::lead.status') *
                                </option>
                                @foreach ($statuses as $status)
                                    <option value="{{ @$status->id }}"
                                        {{ $status->id == $editLead->lead_status_id ? 'selected' : '' }}>
                                        {{ @$status->status_name }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($errors->has('status_id'))
                                <span class="text-danger invalid-select" role="alert" style="display: block">
                                    {{ $errors->first('status_id') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <label class="primary_input_label" for="">@lang('lead::lead.assign') </label>
                            <select class="primary_select" name="assign_id">
                                <option data-display="@lang('lead::lead.assign')" value="">
                                    @lang('lead::lead.assign')
                                </option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ @$staff->id }}"
                                        {{ $staff->id == $editLead->assign_id ? 'selected' : '' }}>
                                        {{ @$staff->full_name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('assign_id'))
                                <span class="text-danger invalid-select" role="alert" style="display: block">
                                    {{ $errors->first('assign_id') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mt-25">
                    <div class="row">
                        @php
                            $mt = '';
                        @endphp
                        @if (moduleStatusCheck('University'))
                            @php
                                $mt = 'mt-25';
                            @endphp
                            @includeIf('university::common.session_faculty_depart_academic_semester_level', [
                                'mt' => $mt,
                                'niceSelect' => 'primary_select',
                                'hide' => ['USUB', 'USEC'],
                                'required' => ['USN', 'UF', 'UD', 'UA', 'US', 'USL'],
                            ])
                        @else
                            <div class="col-lg-4">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="">@lang('common.class')<span
                                            class="text-danger"> *</span></label>
                                    <select class="primary_select" name="class" id="editLeadClass">
                                        <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class')
                                            <span class="text-danger"> *</span></option>

                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ $editLead->lead_class_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->class_name }} </option>
                                        @endforeach

                                    </select>
                                    <div class="pull-right loader loader_style" id="select_edit_lead_class_loader">
                                        <img class="loader_img_style"
                                            src="{{ assetPath('public/backEnd/img/demo_wait.gif') }}" alt="loader">
                                    </div>

                                    @if ($errors->has('class'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('class') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-4 {{ $mt }}">
                            <div class="primary_input">
                                <label class="primary_input_label" for="">@lang('lead::lead.first_name')<span
                                        class="text-danger"> *</span></label>
                                <input class="primary_input_field read-only-input form-control" type="text"
                                    name="first_name" value="{{ $editLead->first_name }}">


                                @if ($errors->has('first_name'))
                                    <span class="text-danger" style="display: block">
                                        {{ $errors->first('first_name') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 {{ $mt }}">
                            <div class="primary_input">
                                <label class="primary_input_label" for="">@lang('lead::lead.last_name')</label>
                                <input class="primary_input_field read-only-input form-control" type="text"
                                    name="last_name" value="{{ $editLead->last_name }}">

                                <span class="text-danger" id="nameError">
                                    @if ($errors->has('last_name'))
                                        <span class="text-danger" style="display: block">
                                            {{ $errors->first('last_name') }}
                                        </span>
                                    @endif
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-12 mt-25">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="primary_input">
                                <label class="primary_input_label" for="">@lang('admin.phone')</label>
                                <input oninput="phoneCheck(this)"
                                    class="primary_input_field read-only-input form-control" type="text"
                                    name="phone" value="{{ $editLead->mobile }}">


                                <span class="text-danger" id="phoneError">
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="primary_input">
                                <label class="primary_input_label"
                                    for="">@lang('admin.email')<span></span></label>

                                <input oninput="emailCheck(this)"
                                    class="primary_input_field read-only-input form-control" type="email"
                                    name="email" value="{{ $editLead->email }}">


                            </div>
                        </div>


                        <div class="col-lg-3">
                            <div class="primary_input">
                                <label class="primary_input_label" for="">@lang('admin.date_of_birth') </label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="">
                                                <input
                                                    class="primary_input_field  primary_input_field date form-control form-control has-content"
                                                    id="startDate" type="text" name="date_of_birth"
                                                    readonly="true"
                                                    value="{{ date('m/d/Y', strtotime($editLead->date_of_birth)) }}"
                                                    required>
                                            </div>
                                        </div>
                                        <button class="btn-date" data-id="#startDate" type="button">
                                            <i class="ti-calendar" id="start-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                                <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="primary_input ">
                                <label class="primary_input_label"
                                    for="">@lang('lead::lead.city')<span></span></label>
                                <select class="primary_select" name="city">
                                    <option data-display="@lang('lead::lead.city')" value="">
                                        @lang('lead::lead.city')</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ $editLead->city_id == $city->id ? 'selected' : '' }}>
                                            {{ $city->city_name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('city'))
                                    <span class="text-danger invalid-select" role="alert" style="display: block">
                                        {{ $errors->first('city') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12 mt-25">
                    @include('lead::lead._lead_custom_field')
                </div>
                <div class="col-lg-12 mt-25">
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="primary_input_label" for="">@lang('admin.description')<span></span> </label>
                            <div class="primary_input">
                                <textarea class="primary_input_field a form-control edit_summernote" cols="0" rows="10"
                                    name="description" id="edit_summernote">{!! $editLead->description !!}</textarea>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-12 text-center mt-20">
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg"
                            data-dismiss="modal">@lang('admin.cancel')</button>
                        <button class="primary-btn fix-gr-bg submit" id="save_button_query"
                            type="submit">@lang('admin.save')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{ html()->form()->close() }}
@include('backEnd.partials.date_picker_css_js')
<script>
    $('form[id="updateLeadForm"]').validate({
        rules: {
            source_id: {
                required: true,
            },
            status_id: {
                required: true,
            },
            first_name: {
                required: true,
            },
            last_name: {
                required: false,
            },
            phone: {
                required: true,
            },
            email: {
                required: false,
            }

        }
    });

    // $(document).ready(function() {
    $("#edit_lead_academic_year").on("change", function() {
        var url = $("#url").val();
        var i = 0;
        // alert('okay');
        var formData = {
            id: $(this).val(),
        };


        // get section for student
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/" + "academic-year-get-class",

            beforeSend: function() {
                $('#select_edit_lead_class_loader').removeClass('loader').addClass('pre_loader');
            },

            success: function(data) {
                $("#leadClass").empty().append(
                    $("<option>", {
                        value: '',
                        text: window.jsLang('select_class') + ' *',
                    })
                );

                if (data[0].length) {
                    $.each(data, function(i, className) {
                        $("#editLeadClass").append(
                            $("<option>", {
                                value: className.id,
                                text: className.class_name,
                            })
                        );
                    });
                }
                $('#editLeadClass').niceSelect('update');
                $('#editLeadClass').trigger('change');
            },
            error: function(data) {
                console.log('Error:', data);
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#select_edit_lead_class_loader').removeClass('pre_loader').addClass(
                    'loader');
                }
            }
        });
    });
    // });
    // $(document).ready(function() {
    //     $('#edit_summernote').summernote();
    // });
    $("#search-icon").on("click", function() {
        $("#search").focus();
    });

    $("#start-date-icon").on("click", function() {
        $("#startDate").focus();
    });

    $("#end-date-icon").on("click", function() {
        $("#endDate").focus();
    });

    $(".primary_input_field.date").datepicker({
        autoclose: true,
        setDate: new Date(),
    });
    $(".primary_input_field.date").on("changeDate", function(ev) {
        // $(this).datepicker('hide');
        $(this).focus();
    });

    $(".primary_input_field.time").datetimepicker({
        format: "LT",
    });

    if ($(".primary_select").length) {
        $(".primary_select").niceSelect();
    }
</script>

<script>
    $('.edit_summernote').summernote({
        placeholder: 'Description',
        tabsize: 2,
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            // ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            // ['insert', ['link', 'picture', 'hr']],
            ['view', ['fullscreen' /*, 'codeview' */ ]], // remove codeview button
            ['help', ['help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                sendFile(files, '.edit_summernote')
            }
        }
    });
</script>
