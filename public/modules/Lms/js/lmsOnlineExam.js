"use strict";

$(document).ready(function() {
    $("#lms_select_class").on("change", function() {

        var url = $("#url").val();
        var i = 0;
        var formData = {
            id: $(this).val(),
        };

        $("#select_section").empty().append(
            $("<option>", {
                value: '',
                text: window.jsLang('select_section'),
            })
        );

        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/" + "ajaxStudentPromoteSection",

            beforeSend: function() {
                $('#select_section_loader').addClass('pre_loader');
                $('#select_section_loader').removeClass('loader');
            },
            success: function(data) {
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#select_section").find("option").not(":first")
                            .remove();
                        $("#select_section_div ul").find("li").not(":first")
                            .remove();

                        $.each(item, function(i, section) {
                            $("#select_section").append(
                                $("<option>", {
                                    value: section.id,
                                    text: section.section_name,
                                })
                            );

                        });
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                $("#select_section").niceSelect('update');
                i--;
                if (i <= 0) {
                    $('#select_section_loader').removeClass('pre_loader');
                    $('#select_section_loader').addClass('loader');
                }
            }
        });

        $("#select_subject").empty().append(
            $("<option>", {
                value: '',
                text: window.jsLang('select_subject') + ' *',
            })
        );
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/" + "ajaxSubjectFromClass",

            beforeSend: function() {
                $('#select_subject_loader').addClass('pre_loader').removeClass(
                'loader');
            },
            success: function(data) {

                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $.each(item, function(i, subject) {
                            $("#select_subject").append(
                                $("<option>", {
                                    value: subject.id,
                                    text: subject.subject_name,
                                })
                            );
                        });
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                $("#select_subject").niceSelect('update');
                i--;
                if (i <= 0) {
                    $('#select_subject_loader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
    });

    // edit_online_exam_page
    let online_exam_id = $("#lesson_online_exam_id").val();
    let duration_type = $("#duration_type_"+online_exam_id).val();
    
    if (duration_type == 'exam') {
        $('#edit_duration_type_exam_div_'+online_exam_id).show();
        $('#edit_duration_type_question_div_'+online_exam_id).hide();
        $('#edit_duration_'+online_exam_id).attr('required', true);
        $('#edit_default_question_time_'+online_exam_id).attr('required', false);
    } else {

        $('#edit_duration_type_exam_div_'+online_exam_id).hide();
        $('#edit_duration_type_question_div_'+online_exam_id).show();

        $('#edit_duration_'+online_exam_id).attr('required', false);
        $('#edit_default_question_time_'+online_exam_id).attr('required', true);
    }

    // edit online_question  page
    $('.multiple_images input[type="file"]').change(function() {
        $(this).closest('.multiple_images').find('.show_file_name');
        $(this).closest('.multiple_images').find('.show_file_name').html('File Selected');
    });
});

// edit_online_exam_page
$(document).on('change', '.edit_duration_type', function() {
    let online_exam_id = $(this).data('online_exam_id');
    let duration_type = $(this).val();
    if (duration_type == 'exam') {
        $('#edit_duration_type_exam_div_'+online_exam_id).show();
        $('#edit_duration_type_question_div_'+online_exam_id).hide();
        $('#edit_duration_'+online_exam_id).attr('required', true);
        $('#edit_default_question_time_'+online_exam_id).attr('required', false);
    } else {

        $('#edit_duration_type_exam_div_'+online_exam_id).hide();
        $('#edit_duration_type_question_div_'+online_exam_id).show();

        $('#edit_duration_'+online_exam_id).attr('required', false);
        $('#edit_default_question_time_'+online_exam_id).attr('required', true);
    }
})

// edit online_question  page
function uploadImage(id) {
    $('.show_file_name' + id).html('File Selected');
    var select_image = $('#question_image' + id);

    var file = document.getElementById("question_image" + id).files[0];
    if (file) {
        if (file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/jpg") {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview' + id).attr('src', e.target.result);
            }
            reader.readAsDataURL(file);

        } else {
            $('.show_file_name' + id).html("Invalid file type");
            $('#question_image' + id).val(null);
        }
    }
}

$('#question_bank_submit').click(function(e) {
    e.preventDefault();
    var ck_box = $('.multiple-images input[type="checkbox"]:checked').length;
    var answer_type = $("#answer_type").val();
    var question_type = $("#question-type").val();

    if (ck_box > 0) {
        if ($("input[name='images[]']").val() == "") {
            toastr.warning('Please Select Valid Option Images', 'Warning', {
                timeOut: 5000
            })
        } else {
            if (answer_type == 'radio' && ck_box > 1) {
                toastr.warning('Please Select One Correct Answer', 'Warning', {
                    timeOut: 5000
                })
            } else {
                $('#question_bank').submit();
            }

        }
    } else {

        if (question_type != 'MI' || question_type != 'PM' || question_type != 'IMQ') {
            $('#question_bank').submit();
        } else {

            toastr.warning('Please Select Correct  Answer', 'Warning', {
                timeOut: 5000
            })
        }
    }
});

$(document).on('click', '.common-checkbox', function() {
    var answer_type = $("#answer_type").val();
    if (answer_type == 'radio') {
        $('.common-checkbox').prop('checked', false);
        $(this).prop('checked', true)
    }
})

CKEDITOR.editorConfig = function(config) {
    config.language = 'es';
    config.uiColor = '#F7B42C';
    config.height = 300;
    config.toolbarCanCollapse = true;
    config.extraPlugins = 'imageuploader';
};

CKEDITOR.replace('suitable_words');

CKEDITOR.replace('question', {
    filebrowserUploadUrl: "{{ route('ckeditor_upload', ['_token' => csrf_token()]) }}",
    filebrowserUploadMethod: 'form',
    on: {
        instanceReady: function() {
            this.dataProcessor.htmlFilter.addRules({
                elements: {
                    img: function(el) {
                        if (!el.attributes.alt)
                            el.attributes.alt = 'Question image';

                        el.addClass('feature_image_ck');
                    }
                }
            });
        }
    }
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#blah').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// online_question_section_inside
$("#imgInp").change(function() {
    readURL(this);
})

$("#search-icon").on("click", function() {
    $("#search").focus();
});

$("#start-date-icon").on("click", function() {
    $("#startDate").focus();
});

$("#end-date-icon").on("click", function() {
    $("#endDate").focus();
});

$(".primary-input.date").datepicker({
    autoclose: true,
    setDate: new Date(),
});
$(".primary-input.date").on("changeDate", function(ev) {
    $(this).focus();
});

$(".primary-input.time").datetimepicker({
    format: "LT",
});

if ($(".niceSelect1").length) {
    $(".niceSelect1").niceSelect();
}

// online_exam_section_add_inside
$(document).on('change', '.duration_type', function() {
    let chapter_id = $(this).data('chapter_id');
    let duration_type = $(this).val();
    if (duration_type == 'exam') {
        $('#duration_type_exam_div_'+chapter_id).show();
        $('#duration_type_question_div_'+chapter_id).hide();
    } else {

        $('#duration_type_exam_div_'+chapter_id).hide();
        $('#duration_type_question_div_'+chapter_id).show();
    }
})

function hideOrShowDurationTypeDiv(chapter_id, duration_type) {

    if (duration_type == 'exam') {
        $('#duration_type_exam_div_'+chapter_id).show();
        $('#duration_type_question_div_'+chapter_id).hide();
    } else {

        $('#duration_type_exam_div_'+chapter_id).hide();
        $('#duration_type_question_div_'+chapter_id).show();
    }
}

// online_exam_section_add
$(document).on('change', '.duration_type_Course', function() {
    let duration_type = $(this).val();

    if (duration_type == 'exam') {
        $('#duration_type_exam_div').show();
        $('#duration_type_question_div').hide();

    } else {
        $('#duration_type_exam_div').hide();
        $('#duration_type_question_div').show();

    }
})