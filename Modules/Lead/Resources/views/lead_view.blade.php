@extends('backEnd.master')
@section('title')
    @lang('lead::lead.lead_details')
@endsection
@section('mainContent')
    <style>
        .client_img {
            max-width: 50px;
            border-radius: 50%;
            height: 50px;
        }

        .gripGap {
            grid-gap: 20px;
        }

        /* .dataTables_filter > label {
            display: none;
        } */
        /* .student-activities .single-activity .sub-activity:before,
        .student-activities .single-activity .title:after, .student-activities .single-activity .sub-activity:after{
            display: none
        } */
        /* .sub-activity-box{
            grid-gap: 20px;
        } */

    </style>
    <input type="hidden" value="{{ $lead->id }}" id="main_lead_id" />


    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('lead::lead.lead_details')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="{{ route('lead.index') }}">@lang('lead::lead.lead')</a>
                    <a href="#">@lang('lead::lead.lead_details')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 student-details up_admin_visitor">
                    <ul class="nav nav-tabs tabs_scroll_nav" role="tablist">
                        @if (userPermission(1416))
                            <li class="nav-item ">
                                <a class="nav-link active" href="#notes" role="tab"
                                    data-toggle="tab">@lang('lead::lead.notes')</a>
                            </li>
                        @endif
                        @if (userPermission(1417))
                            <li class="nav-item">
                                <a class="nav-link" href="#reminders" role="tab"
                                    data-toggle="tab">@lang('lead::lead.reminders')</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link lead_activity" href="#activity_log" role="tab"
                                data-toggle="tab">@lang('lead::lead.activity_log')</a>
                        </li>

                        <li class="nav-item edit-button">

                            @if (userPermission(1418) && $alreadyConverted == null)
                                <a class="primary-btn small fix-gr-bg" target="_blank"
                                    href="{{ route('lead.convert-student', [@$lead->id]) }}">{{ __('lead::lead.convert_to_student') }}
                                </a>
                            @endif

                            @if ($alreadyConverted)
                                <a class="primary-btn small fix-gr-bg"
                                    href="#">{{ __('lead::lead.already_converted_student') }}

                                </a>
                            @endif
                        </li>
                    </ul>


                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- End reminders Tab -->
                        @if (userPermission(1416))
                            <div role="tabpanel" class="tab-pane fade active show" id="notes">
                                <div class="white-box">
                                    <!-- Start notes Tab -->
                                    @includeIf('lead::tab.note_tab')
                                    <!-- End notes Tab -->
                                </div>
                            </div>
                        @endif

                        <!-- Start reminders Tab -->

                        @if (userPermission(1417))
                            <div role="tabpanel" class="tab-pane fade" id="reminders">
                                <div class="white-box">
                                    <div class="text-right mb-40">
                                        <button type="button" data-toggle="modal" data-target="#add_reminder_modal"
                                            class="primary-btn tr-bg text-uppercase bord-rad">
                                            <span class="flaticon-notification"></span>
                                            @lang('lead::lead.set_reminder')
                                        </button>
                                    </div>
                                    <div class="" id="reminder_list">
                                        @includeIf('lead::lead.reminder_list')
                                    </div>

                                </div>
                            </div>
                        @endif

                        <!-- Start activity_log Tab -->

                        <div rfole="tabpanel" class="tab-pane fade" id="activity_log">
                            <div class="white-box" id="lead_activity">
                                @includeIf('lead::tab.activity_tab')
                            </div>
                        </div>
                        <!-- End activity_log Tab -->



                    </div>





                </div>


                @includeIf('lead::lead._details')

                <!-- Start Student Details -->

            </div>
            <!-- End lead Details -->
        </div>


    </section>

    <div class="modal fade admin-query" id="add_notes">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> @lang('lead::lead.add_note')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="" method="POST" id="add_note_form">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-12 ">
                                            <div class="primary_input">
                                                <textarea class="primary_input_field form-control{{ @$errors->has('note') ? ' is-invalid' : '' }}" cols="0" rows="3"
                                                    name="note" id="leadNote" required></textarea>
                                                <label class="primary_input_label" for="">@lang('common.description')<span></span> </label>
                                                

                                                @if ($errors->has('note'))
                                                    <span class="text-danger" >
                                                        {{ $errors->first('note') }}</span>
                                                @endif
                                            </div>
                                            <span class="text-danger"  id="add_note_error"></span>
                                        </div>
                                        <div class="col-lg-12 mt-30 d-none" id="noteContactDiv">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="primary_input">
                                                        <input class="primary_input_field  primary_input_field date form-control form-control" type="text"
                                                            name="date_time" value="{{ date('m/d/Y') }}">
                                                        <label class="primary_input_label" for="">@lang('common.date')</label>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="" type="button">
                                                        <i class="ti-calendar" id="start-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-30 d-none" id="noteContactTimeDiv">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="primary_input">
                                                        <input class="primary_input_field primary_input_field time form-control" type="text"
                                                            name="time" id="time">
                                                        <label class="primary_input_label" for="">@lang('common.time') <span class="text-danger"> *</span></label>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="" type="button">
                                                        <i class="ti-timer"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-25">
                                            <input type="radio" name="contact" id="noContact" value="1"
                                                class="common-radio noteContact relationButton" checked>
                                            <label
                                                for="noContact">@lang('lead::lead.i_have_not_contacted_this_lead')</label>
                                        </div>
                                        <div class="col-md-12 mt-10">
                                            <input type="radio" name="contact" id="contact" value="0"
                                                class="common-radio noteContact relationButton">
                                            <label for="contact">@lang('lead::lead.i_got_in_touch_with_this_lead')</label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-12 text-center mt-40">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button type="button" class="primary-btn tr-bg"
                                            data-dismiss="modal">@lang('common.cancel')</button>
                                        <button class="primary-btn fix-gr-bg submitLeadNote"
                                            id="submitLeadNote">@lang('common.save')</button>
                                        <button class="primary-btn fix-gr-bg d-none"
                                            id="submitLeadNoteAfter">@lang('common.save').....</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- add reminder --}}
    <div class="modal fade admin-query" id="add_reminder_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('lead::lead.set_reminder')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="" method="POST" id="reminderForm">
                            <div class="row">
                                <div class="col-lg-12">                                    
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">@lang('common.date') <span class="text-danger"> *</span> </label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input class="primary_input_field  primary_input_field date form-control form-control" id="date_time" type="text"
                                                        name="date_time" value="{{ date('m/d/Y') }}">
                                                    </div>
                                                </div>
                                                <button class="btn-date" data-id="#startDate" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('date') }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-15">
                            
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">@lang('common.time')<span class="text-danger"> *</span></label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input class="primary_input_field primary_input_field time form-control" type="text" name="time"
                                                    id="reminder_time">
                                                            
                                                        @if ($errors->has('in_time'))
                                                        <span class="text-danger d-block" >
                                                            {{ $errors->first('in_time') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-alarm-clock " id="admission-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-15">
                                    <label class="primary_input_label" for="">@lang('lead::lead.assign')<span class="text-danger"> *</span></label>
                                    <select class="primary_select " name="reminder_to" id="reminder_to">
                                        <option data-display="@lang('lead::lead.assign') *" value="">
                                            @lang('lead::lead.assign') <span class="text-danger"> *</span>
                                        </option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ @$staff->id }}">{{ @$staff->full_name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    @if ($errors->has('assign_id'))
                                        <span class="text-danger invalid-select" role="alert" style="display: block">
                                            {{ $errors->first('assign_id') }}
                                        </span>
                                    @endif
                                    <span class="text-danger"  id="add_assign_error"></span>

                                </div>
                                <div class="col-lg-12 mt-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">@lang('common.description') <span class="text-danger"> *</span> </label>
                                        <textarea class="primary_input_field form-control" cols="0" rows="3" name="description" id="reminder_description"></textarea>

                                    </div>
                                    <span class="text-danger"  id="add_reminder_error"></span>

                                </div>


                                <div class="col-lg-12 mt-15">

                                    <input type="checkbox" id="email_to" class="common-checkbox email_to" name="email_to">
                                    <label for="email_to">@lang('lead::lead.send_also_an_email_for_this_reminder')</label>
                                </div>



                                <div class="col-lg-12 text-center mt-15">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button type="button" class="primary-btn tr-bg"
                                            data-dismiss="modal">@lang('common.cancel')</button>

                                        <button class="primary-btn fix-gr-bg submit"
                                            id="submitLeadReminder">@lang('common.save')</button>
                                        <button class="primary-btn fix-gr-bg d-none"
                                            id="saveButtonAfterClick">@lang('lead::lead.saving')......</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- end reminder --}}
    @include('backEnd.partials.date_picker_css_js')
    <script>
        function deleteDoc(id, doc) {
            // alert(doc);
            var modal = $('#delete-doc');
            modal.find('input[name=student_id]').val(id)
            modal.find('input[name=doc_id]').val(doc)
            modal.modal('show');
        }
    </script>
@endsection
@section('script')
    {{-- <script type="text/javascript">
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });
     </script> --}}
    {{-- <script src="{{assetPath('modules/Lead/Resources/assets/js/app.js')}}"></script> --}}
    <script>
        $(document).on('click', '.lead_activity', function(e) {
            e.preventDefault();

            $('#notes').removeClass('active show');
            $('#reminders').removeClass('show');
            $('#activity_log').addClass('show');

            let lead_id = $('#main_lead_id').val();

            var formData = {

                lead_id: lead_id,

            };
            $.ajax({
                type: "POST",
                data: formData,
                dataType: "html",
                url: "/lead/lead-activity",


                success: function(data) {

                    $('#lead_activity').html(data);
                    //  $('#activity_log').addClass('active show');


                },

                error: function(data) {

                }


            });
        });
        // lead note js
        $(document).on('click', '#submitLeadNote', function(event) {
            event.preventDefault();


            let url = $("#url").val();
            let lead_id = $('#main_lead_id').val();
            let note = $('#leadNote').val();
            let date_time = $('#date_time').val();
            let time = $('#time').val();
            let contact = $("input[name='contact']:checked").val();

            if (note == '') {
                $('#add_note_error').html('Note Field Can not be empty');
            }
            if (note != '') {
                $('#add_note_error').html('');
                $('#submitLeadNote').addClass('d-none')
                $('#submitLeadNoteAfter').removeClass('d-none');
                var formData = {
                    note: note,
                    lead_id: lead_id,
                    date_time: date_time,
                    time: time,
                    contact: contact,
                };

                // console.log(formData);
                $.ajax({
                    type: "post",
                    data: formData,
                    dataType: "html",
                    url: url + "/lead/note-store",
                    success: function(data) {
                        $('#note_list').html(data);
                        // emptyField();
                        setTimeout(function() {
                            toastr.success("Note Created Operation Successfully", "Success", {
                                timeOut: 5000,
                            });
                        }, 500);
                        $('#add_notes').modal('hide');
                        $('#noteContactDiv').hide();
                        $('#add_note_form').trigger('reset');
                        $('#submitLeadNote').removeClass('d-none')
                        $('#submitLeadNoteAfter').addClass('d-none');
                    },


                    error: function(data) {
                        setTimeout(function() {
                            toastr.error("Operation Not Done!", "Error Alert", {
                                timeOut: 5000,
                            });
                        }, 500);
                    },
                });
            }
        });
        $(document).on('click', '.editNoteModal', function(event) {
            event.preventDefault();
            // alert('ok');
            $('#editNoteModalShow').modal('show');
            let note_id = $(this).data('note_id');
            let edit_note = $(this).data('note');
            $('#edit_note').val(edit_note);
            $('#edit_note_id').val(note_id);
        });

        $(document).on('click', '.updateLeadNote', function(event) {
            event.preventDefault();

            let url = $("#url").val();
            let lead_id = $("#main_lead_id").val();
            let note_id = $('#edit_note_id').val();
            let edit_note = $('#edit_note').val();
            // alert(edit_note);
            if (edit_note == '') {
                $('#edit_note_error').html('Note Field Can not be empty');
            }
            if (edit_note != '') {
                $('#updateLeadNote').addClass('d-none')
                $('#updateLeadNoteAfter').removeClass('d-none');
                var formData = {
                    note_id: note_id,
                    edit_note: edit_note,
                    lead_id: lead_id,
                };
                $.ajax({
                    type: "post",
                    data: formData,
                    dataType: "html",
                    url: url + "/lead/note-update",
                    success: function(data) {
                        // $('#note_list').html(data);
                        // emptyField();
                        setTimeout(function() {
                            toastr.success("Note Update Operation Successfully", "Success", {
                                timeOut: 2000,
                            });
                        }, 2000);
                        $('#editNoteModalShow').modal('hide');
                        $('#editNoteForm').trigger('reset');
                        $('#updateLeadNote').removeClass('d-none')
                        $('#updateLeadNoteAfter').addClass('d-none');
                        setTimeout(function() {
                            $('#note_list').html(data);
                        }, 1000);
                    },


                    error: function(data) {
                        setTimeout(function() {
                            toastr.error("Operation Not Done!", "Error Alert", {
                                timeOut: 5000,
                            });
                        }, 500);
                    },
                });
            }
        });

        $(document).on('click', '.deleteNoteModal', function(event) {
            event.preventDefault();
            $('#deleteNoteModal').modal('show');
            let note_id = $(this).data('note_id');
            $('#note_id_for_delete').val(note_id);

        });

        $(document).on('click', '.deleteLeadNoteButton', function(event) {
            event.preventDefault();
            $('#deleteLeadNoteButton').addClass('d-none')
            $('#deleteLeadNoteButtonAfter').removeClass('d-none');

            let lead_id = $("#main_lead_id").val();
            let note_id = $("#note_id_for_delete").val();
            // alert(note_id);
            var formData = {
                note_id: note_id,
                lead_id: lead_id,
            };
            // console.log(formData);
            $.ajax({
                type: "post",
                data: formData,
                dataType: "html",
                url: "{{ route('lead.note.delete') }}",
                success: function(data) {
                    // $('#note_list').html(data);
                    // emptyField();
                    setTimeout(function() {
                        toastr.success("Note Delete Operation Successfully", "Success", {
                            timeOut: 3000,
                        });
                    }, 300);
                    $('#deleteNoteModal').modal('hide');
                    $('#deleteLeadNoteButton').removeClass('d-none')
                    $('#deleteLeadNoteButtonAfter').addClass('d-none');
                    setTimeout(function() {
                        $('#note_list').html(data);
                    }, 1000);
                },


                error: function(data) {
                    setTimeout(function() {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });

        function emptyField() {
            $('#edit_note').val('');
            $('#leadNote').val('');
            $('.primary_input_field').removeClass('has-content');
            $('#reminder_description').val('');
            // $('#reminder_to').val('');
            $('#email_to').val('');
        }
        $(document).on('change', '.noteContact', function() {
            let contact = $(this).val();
            if (contact == 1) {
                $('#noteContactDiv').addClass('d-none');
                $('#noteContactTimeDiv').addClass('d-none');
            } else {
                $('#noteContactDiv').removeClass('d-none');
                $('#noteContactTimeDiv').removeClass('d-none');
            }
        });
        //end lead note js

        //lead reminder js
        $(document).on('click', '#submitLeadReminder', function(event) {
            event.preventDefault();
            // $('#submitLeadReminder').prop('disabled',true);


            let description = $('#reminder_description').val();
            let lead_id = $('#main_lead_id').val();
            let date_time = $('#date_time').val();
            let time = $('#reminder_time').val();
            let reminder_to = $('#reminder_to').val();
            let is_checked = $(".email_to").is(':checked');
            if (is_checked) {
                var email_to = 1;
            } else {
                var email_to = 0;
            }

            if (description == '') {
                $('#add_reminder_error').html('Description Field Can not be empty');

            }
            if (date_time == '') {
                $('#add_date_error').html('Date Field Can not be empty');

            }

            if (time == '') {
                $('#add_time_error').html('Time Field Can not be empty');

            }

            if (reminder_to == '') {
                $('#add_assign_error').html('Reminder Field Can not be empty');

            }

            if (description != '' && date_time != '' && time != '' && reminder_to != '') {


                $('#saveButtonAfterClick').removeClass('d-none')
                $('#submitLeadReminder').addClass('d-none');
                //alert(lead_id);
                var formData = {
                    lead_id: lead_id,
                    description: description,
                    date_time: date_time,
                    reminder_to: reminder_to,
                    email_to: email_to,
                    time: time,

                };

                $.ajax({
                    type: "POST",
                    data: formData,
                    dataType: 'html',
                    url: "{{ route('lead.reminder-store') }}",
                    success: function(data) {
                        $('#reminder_list').html(data);
                        //  emptyField();
                        setTimeout(function() {
                            toastr.success("Reminder Created Operation Successfully",
                            "Success", {
                                timeOut: 5000,
                            });
                        }, 500);
                        $('#add_reminder_modal').modal('hide');
                        $('#reminderForm').trigger('reset');


                        $('#saveButtonAfterClick').addClass('d-none')
                        $('#submitLeadReminder').removeClass('d-none');
                        $('#add_reminder_error').html('');
                        $('#add_date_error').html('');
                        $('#add_time_error').html('');
                        $('#add_assign_error').html('');

                    },


                    error: function(data) {
                        setTimeout(function() {
                            toastr.error("Operation Not Done!", "Error Alert", {
                                timeOut: 5000,
                            });
                        }, 500);
                        $('#saveButtonAfterClick').addClass('d-none')
                        $('#submitLeadReminder').removeClass('d-none');
                    },
                });
            }
        });

        //edit lead reminder js
        $(document).on('click', '.updateLeadReminder', function(event) {
            event.preventDefault();
            // $('#submitLeadReminder').prop('disabled',true);

            let reminder_id = $('#edit_reminder_id').val();
            let description = $('#edit_reminder_description').val();
            let lead_id = $('#main_lead_id').val();
            let date_time = $('#date_time').val();
            let time = $('#edit_time').val();
            let reminder_to = $('#edit_reminder_to').val();
            // let email_to = $('#edit_email_to').val();
            let is_checked = $("#edit_email_to").is(':checked');
            // alert(is_checked);
            if (is_checked) {
                var email_to = 1;
            } else {
                var email_to = 0;
            }

            if (description == '') {
                $('#edit_reminder_error').html('Description Field Can not be empty');

            }
            if (date_time == '') {
                $('#edit_date_error').html('Date Field Can not be empty');

            }
            if (time == '') {
                $('#edit_time_error').html('Time Field Can not be empty');

            }
            if (reminder_to == '') {
                $('#edit_assign_error').html('Reminder Field Can not be empty');

            }
            if (description != '' && date_time != '' && time != '' && reminder_to != '') {
                $('#updateReminderButtonAfterClick').removeClass('d-none')
                $('#updateLeadReminder').addClass('d-none');
                var formData = {
                    reminder_id: reminder_id,
                    lead_id: lead_id,
                    description: description,
                    date_time: date_time,
                    time: time,
                    reminder_to: reminder_to,
                    email_to: email_to,
                };
                console.log(formData);
                $.ajax({
                    type: "POST",
                    data: formData,
                    dataType: 'html',
                    url: "{{ route('lead.reminder-update') }}",
                    success: function(data) {
                        // $('#reminder_list').html(data);
                        //  emptyField();
                        setTimeout(function() {
                            toastr.success("Reminder Updated Operation Successfully",
                            "Success", {
                                timeOut: 5000,
                            });
                        }, 500);
                        $('#showDetaildModal').modal('hide');
                        $('#editReminderForm').trigger('reset');

                        $('#updateReminderButtonAfterClick').addClass('d-none')
                        $('#updateLeadReminder').removeClass('d-none');
                        setTimeout(function() {
                            $('#reminder_list').html(data);
                        }, 1000);
                    },


                    error: function(data) {
                        setTimeout(function() {
                            toastr.error("Operation Not Done!", "Error Alert", {
                                timeOut: 5000,
                            });
                        }, 500);
                    },
                });
            }
        });

        // reminder delete 

        $(document).on('click', '.deleteLeadReminderModal', function(event) {
            event.preventDefault();
            $('#deleteLeadReminderModal').modal('show');
            let lead_id = $(this).data('lead_id');
            let reminder_id = $(this).data('reminder_id');

            $('#lead_id').val(lead_id);
            $('#reminder_id_delete').val(reminder_id);

        });
        $(document).on('click', '.deleteLeadReminderButton', function(event) {
            event.preventDefault();
            $('#deleteLeadReminderButton').addClass('d-none');
            $('#reminderLoadingButton').removeClass('d-none');

            let lead_id = $('#lead_id').val();
            let reminder_id = $('#reminder_id_delete').val();

            var formData = {
                reminder_id: reminder_id,
                lead_id: lead_id,
            };
            console.log(formData);
            $.ajax({
                type: "post",
                data: formData,
                dataType: "html",
                url: " {{ route('lead.reminder-delete') }}",
                success: function(data) {

                    setTimeout(function() {
                        toastr.success("Reminder Delete Operation Successfully", "Success", {
                            timeOut: 5000,
                        });
                    }, 500);
                    $('#deleteLeadReminderModal').modal('hide');
                    $('#deleteReminderForm').trigger('reset');
                    $('#reminderLoadingButton').addClass('d-none');
                    $('#deleteLeadReminderButton').removeClass('d-none');
                    setTimeout(function() {
                        $('#reminder_list').html(data);
                    }, 1000);
                },


                error: function(data) {
                    setTimeout(function() {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        })
        //end reminder js
    </script>
@endsection
