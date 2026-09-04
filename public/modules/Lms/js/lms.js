
"use strict";

function resetTopicType(){

}

function switchAction(e){
    let element=$(e);
    let url = lms_switch_action_url;
    let id = element.data('id');
    let table = element.data('table');
    let column_name = element.data('column_name');
    let value = element.prop('checked') == true ? 1 : 0;
    $.ajax({
        method: "get",
        url: url,
        data: {
            id: id,
            table: table,
            column_name: column_name,
            value: value,
        },
        success: function(data) {
            if (data.success) {
                toastr.success(data.message,"Successful", { timeOut: 5000,});
            } else {
                toastr.error(data.message,"Failed", { timeOut: 5000,}); 
            }
        },
        error: function(data) {
            toastr.error(data.message,"Failed", { timeOut: 5000,}); 
        },
    });
}
function summernoteInit(element, selector, height = 188, placeholder = '') {
    element.summernote({
        placeholder: placeholder,
        tabsize: 2,
        height: height,
        tooltip: false,
        callbacks: {
            onImageUpload: function (files) {
                sendFile(files, selector, element.attr('name'))
            }
        },
        toolbar: [

            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']], ['color', ['color']],
            ['height', ['height']],
            ['insert', ['link', 'picture', 'video', 'math', 'hr']],
            ['view', ['fullscreen']],]

    });
}
$(".topic_type").on('change', function() {
    let type = $(this).val();
    if (type == 1) {
        $('.course_sction').show();
        $('.quiz_section').hide();
    } else {
        $('.course_sction').hide();
        $('.quiz_section').show();
    }
});
$(document).ready(function() {
    
    
    resetTopicType();
    if ($("#previous_type").length > 0) {
        
        let previous_type = $("#previous_type").val();
        if (previous_type == 1) {
            $('.course_sction').show();
            $('.quiz_section').hide();
        } else {
            $('.course_sction').hide();
            $('.quiz_section').show();
        }
    }else{
        $('.quiz_section').hide();
    }

    $.each($('.lms_summernote'), function () {
        summernoteInit($(this), '.lms_summernote');
    });
});

$(document).ready(function () {
    let paymentValue= $('#addLmsPaymentMethod').val();
    paymentOption(paymentValue);

    $("#addLmsPaymentMethod").on("change", function() {
        let paymentValue = $(this).val();
        paymentOption(paymentValue);
    });
});

function paymentOption(paymentValue){
    if( paymentValue== "Bank"){
        $('.AddLmsChequeBank').removeClass('d-none');
        $('.addLmsBank').removeClass('d-none');
        $('.addLms').removeClass('d-none');
        $('.stripeInfo').addClass('d-none');
    }else if(paymentValue == "Cheque"){
        $('.AddLmsChequeBank').removeClass('d-none');
        $('.addLmsBank').addClass('d-none');
        $('.addLms').removeClass('d-none');
        $('.stripeInfo').addClass('d-none');
    }else if(paymentValue == "Stripe"){
        $('.AddLmsChequeBank').addClass('d-none');
        $('.addLmsBank').addClass('d-none');
        $('.stripeInfo').removeClass('d-none');
    }else{
        $('.AddLmsChequeBank').addClass('d-none');
        $('.addLmsBank').addClass('d-none');
        $('.stripeInfo').addClass('d-none');
    }
}

// Student Checkout Payment End

// Add Payment
$('#addLmsAmount').on('submit', function(e) {
    e.preventDefault();
    $('.addLms').attr("disabled","disabled");
    const submit_url = $('#addLmsAmount').attr('action');
    const method = $('#addLmsAmount').attr('method');
    //Start Ajax
    const formData = new FormData($('#addLmsAmount')[0]);
    $.ajax({
        url: submit_url,
        type: method,
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'JSON',
        success: function(response) {
            if(response.goto){
               window.location.href=response.goto;
            }else{
               location.reload();
               toastr.success("Save Successfully","Successful", { timeOut: 5000,});
            }
        },
        error:function (xhr){
            $('#paymentMethodError').html(xhr.responseJSON.errors.payment_method);
            $('#bankError').html(xhr.responseJSON.errors.bank);
            $('#fileError').html(xhr.responseJSON.errors.file);
            $('#nameOnCardError').html(xhr.responseJSON.errors.name_on_card);
            $('#cardNumberError').html(xhr.responseJSON.errors.card_number);
            $('#cvcError').html(xhr.responseJSON.errors.card_cvc);
            $('#expirationMonthError').html(xhr.responseJSON.errors.card_expiry_month);
            $('#expirationYearError').html(xhr.responseJSON.errors.card_expiry_year);
            $('#walletError').html(xhr.responseJSON.errors.wallet_null);
            $('#walletError').html(xhr.responseJSON.errors.wallet_exceed);
            $('.addLms').prop("disabled", false);
        }
    });
});


// Course Enrolement
$(document).ready(function () {
    let courseClassValue= $('#lmsCourseSelectClass').val();
    lmsCourseClass(courseClassValue);

    $("#lmsCourseSelectClass").on("change", function() {
        let courseClassValue = $(this).val();
        lmsCourseClass(courseClassValue);
    });
});

function lmsCourseClass(courseClassValue){
    if( courseClassValue== "all_classes"){
        $('#selectLmsSection').val('');
        $('#selectSectionss').val('');
        $('.lmsSection').addClass('d-none');
        $('.lmsStudent').addClass('d-none');
    }else{
        $('.lmsSection').removeClass('d-none');
        $('.lmsStudent').removeClass('d-none');
    }
}


$(document).ready(function () {
    let courseSectionValue= $('#selectLmsSection').val();
    lmsCourseSection(courseSectionValue);

    $("#selectLmsSection").on("change", function() {
        let courseSectionValue = $(this).val();
        lmsCourseSection(courseSectionValue);
    });
});

function lmsCourseSection(courseSectionValue){
    if( courseSectionValue== "all_section"){
        $('#selectSectionss').val('');
        $('.lmsStudent').addClass('d-none');
    }else{
        $('.lmsStudent').removeClass('d-none');
    }
}

$(document).ready(function() {
    $("#lmsCourseSelectClass").on("change", function() {
        let url = $("#selectLmsSectionUrl").val();
        let shift = $("#shift_id_lms").val();
        let i = 0;
        let classId = $(this).val();
        let shiftId = shift;

        if (classId != 'all_classes') {
            $.ajax({
                method: "get",
                url: url,
                data: {
                    id: classId,
                    shiftId: shiftId,
                },
                beforeSend: function() {
                    $('#lmsCourseSectionLoader').addClass('pre_loader');
                    $('#lmsCourseSectionLoader').removeClass('loader');
                },
                success: function(data) {
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $("#selectLmsSection").find("option").not(":first").remove();
                            $("#selectLmsSectionDiv ul").find("li").not(":first").remove();

                            $("#selectLmsSection").append(
                                $("<option>", {
                                    value: 'all_section',
                                    text: "All Section",
                                })
                            );

                            $.each(item, function(i, allSection) {
                                $("#selectLmsSection").append(
                                    $("<option>", {
                                        value: allSection.id,
                                        text: allSection.section_name,
                                    })
                                );
                            });

                            $(".custom_lms_select_all_hide").css("display", "block");

                            $("#selectLmsSection").niceSelect('update');
                        } else {
                            $("#selectLmsSection").find("option").not(":first").remove();
                            $("#selectLmsSectionDiv ul").find("li").not(":first").remove();
                        }
                    });
                },
                error: function(data) {},
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $('#lmsCourseSectionLoader').removeClass('pre_loader');
                        $('#lmsCourseSectionLoader').addClass('loader');
                    }
                }
            });
        } else {
            $(".custom_lms_select_all_hide").css("display", "none");
        }
    });

    $("#selectLmsSection").on("change", function() {
        let url = $("#selectLmsStudentUrl").val();
        let i = 0;
        let sectionId = $(this).val();
        let classId = $("#lmsCourseSelectClass").val();
        let courseId = $("#course").val();

        if (sectionId != 'all_section') {
            $.ajax({
                method: "get",
                url: url,
                data: {
                    class: classId,
                    section: sectionId,
                    course_id: courseId,
                },
                success: function(data) {
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $("#selectSectionss").find("option").not(":first").remove();
                            $("#selectLmsStudentDiv ul").find("li").not(":first").remove();

                            $.each(item, function(i, allStudent) {
                                $("#selectSectionss").append(
                                    $("<option>", {
                                        value: allStudent.id,
                                        text: allStudent.full_name,
                                    })
                                );
                            });

                            $(".custom_lms_select_all_hide").css("display", "block");
                        } else {
                            $("#selectLmsStudentDiv .current").html("SELECT SECTION *");
                            $("#selectSectionss").find("option").not(":first").remove();
                            $("#selectLmsStudentDiv ul").find("li").not(":first").remove();
                        }
                    });
                    $('#selectSectionss').multiselect('reset');
                },
                error: function(data) {},
            });
        } else {
            $(".custom_lms_select_all_hide").css("display", "none");
        }
    });
});
