"use strict";

$(document).ready(function() {
    $('input[type="file"]').change(function(e) {
        let fileName = e.target.files[0] ? e.target.files[0].name : '';
        if ($(this).attr('id') === 'homework_file') {
            $('#placeholderHomeworkName53').val(fileName);
        } else if ($(this).attr('id') === 'document_file_2') {
            $('#placeholderFileDocument559').val(fileName);
        } else {
            let key = $(this).attr('id').replace('document_file_lesson', '');
            $('#filePlaceholder' + key).val(fileName);
        }
    });

    if ($("input[name='is_free']:checked").val()) {
        var status = "1";
    } else {
        var status = "0";
    }
    if (status == 1) {
        $("#courseFee").addClass("d-none");
        $("#courseFeeDiscount").addClass("d-none");
    } else {
        $("#courseFee").removeClass("d-none");
        $("#courseFeeDiscount").removeClass("d-none");
    }
    
    $('.course_switch_btn').on('change', function () {
        if ($("input[name='is_free']:checked").val()) {
            var status = "1";
        } else {
            var status = "0";
        }
        if (status == 1) {
            $("#courseFee").addClass("d-none");
            $("#courseFeeDiscount").addClass("d-none");
        } else {
            $("#courseFee").removeClass("d-none");
            $("#courseFeeDiscount").removeClass("d-none");
        }
    });

    var data = $("#select_class").val();
    toggleFields(data);

    $("#select_class").on("change", function() {
        var selected = $(this).val();
        toggleFields(selected);
    });

    function toggleFields(value) {
        if (value == "all_class") {
            $("#select_section_div").css("display", "none");
            $("#select_subject_div").css("display", "none");
        } else {
            $("#select_section_div").css("display", "block");
            $("#select_subject_div").css("display", "block");
        }
    }

    var data = $("#lms_select_class").val();
    if (data == "all_class") {
        $("#class_all").removeClass("d-none");
        $("#select_section_div").css("display", "none");
        $("#select_subject_div").addClass("d-none");

    } else {
        $("#class_all").addClass("d-none");
        $("#select_section_div").css("display", "block");
        $("#select_subject_div").removeClass("d-none");
    }
    $("#lms_select_class").on("change", function() {
        var selected = $(this).val();

        if (selected == "all_class") {
            $("#class_all").removeClass("d-none");
            $("#select_section_div").css("display", "none");
            $("#select_subject_div").addClass("d-none");
        } else {
            $("#class_all").addClass("d-none");
            $("#select_section_div").css("display", "block");
            $("#select_subject_div").removeClass("d-none");
        }
    });

    $('.deleteCourseFile').on('click', function() {
        var url = $("#url").val();
        let id = $(this).data('id');
        $('#course_file_delete_id').val(id);
        $('#deleteCourseFile').modal('show');
    })

    $('input[type="file"]').change(function(e) {
        let fileName = e.target.files[0] ? e.target.files[0].name : '';
        if ($(this).attr('id') === 'document_file_55') {
            $('#placeholderFileDocument55').val(fileName);
        }
    });

    $('input[type="file"]').change(function(e) {
        let fileName = e.target.files[0] ? e.target.files[0].name : '';
        let inputId = $(this).attr('id');
        
        let key = inputId.replace('homework_file', '');
        if (key) {
            $('#placeholderHomeworkName' + key).val(fileName);
        }
    });

    if($('input[type="file"]').length){
        $('input[type="file"]').change(function(e) {
            let fileName = e.target.files[0] ? e.target.files[0].name : '';
            if ($(this).attr('id') === 'homework_file') {
                $('#placeholderHomeworkName51').val(fileName);
            } else if ($(this).attr('id') === 'document_file_2') {
                $('#placeholderFileDocument').val(fileName);
            } else {
                let key = $(this).attr('id').replace('document_file_lesson', '');
                $('#filePlaceholder' + key).val(fileName);
            }
        });
    }
    

    // edit_quiz page
    let type = $(this).data('type');
    let divId = $(this).data('id');
    hideOrShowQuizDiv(type, divId);

    $(document).on('change', '.lessonQuestion', function() {
        let chapter_id = $(this).data('chapter_id');
        let course_id = $("#course_id").val();
        let lesson_id = $(this).val();
        var url = $("#url").val();
        if(!lesson_id) {
            return;
        }
        
        $("#checkbox_section_"+chapter_id).prop("checked", false);            
        var formData = {
            chapter_id : chapter_id,           
            course_id  : course_id,           
            lesson_id  : lesson_id,           
        };
        $("#selectSectionLmsQuiz_"+chapter_id).val("");

        $("#selectSectionLmsQuiz_"+chapter_id).niceSelect("update");
            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/lms/" + "get-question",
                success: function (data) {
                var a = "";
                $.each(data, function (i, item) {
                    if (item.length) {
                        $("#selectSectionLmsQuiz_"+chapter_id).find("option").remove();
                        $("#selectSectionLmsQuizDiv_"+chapter_id+" ul").find("li").not(":first").remove();
                        $.each(item, function (i, question) {
                            $("#selectSectionLmsQuiz_"+chapter_id).append(
                                $("<option>", {
                                    value: question.id,
                                    text: question.question,
                                })
                            );
                        
                        });
                    } else {
                        $("#selectSectionLmsQuizDiv_"+chapter_id+" .current").html("SELECT Question Group *");
                        $("#selectSectionLmsQuiz_"+chapter_id).find("option").not(":first").remove();
                        $("#selectSectionLmsQuizDiv_"+chapter_id+" ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function (data) {
                console.log("Error:", data);
            },
        });
    })

    $(".selectSectionLmsQuiz").on("select2:opening select2:closing", function(event) {
        var $searchfield = $(this).parent().find(".select2-search__field");
        $searchfield.prop("disabled", true);
        });

    $(".checkbox_section").click(function() {
        if ($(".checkbox_section").is(":checked")) {
            $(".selectSectionLmsQuiz").find("option").prop("selected", true);
            $(".selectSectionLmsQuiz").trigger("change");
        } else {
            $(".selectSectionLmsQuiz").find("option").prop("selected", false);
            $(".selectSectionLmsQuiz").trigger("change");
        }
    });

    // lesson_section_inside page

    $('input[type="file"]').change(function(e) {
        let key = $(this).attr('id').replace('document_file_lesson', '');
        let fileName = e.target.files[0] ? e.target.files[0].name : '';
        $('#filePlaceholder' + key).val(fileName);
    });

    function handleHostChange(key, host_id, upload_type) {
        $("#lesson_iframeBox_last" + key).addClass("d-none");
        $("#lesson_videoUrl_last" + key).addClass("d-none");
        $("#lesson_vimeoUrl_last" + key).addClass("d-none");
        $("#lesson_fileupload_last" + key).addClass("d-none");

        if (host_id == 2 || host_id == 3) {
            $("#lesson_videoUrl_last" + key).removeClass("d-none");
        } else if (host_id == 4) {
            if (upload_type === "Direct") {
                $("#lesson_fileupload_last" + key).removeClass("d-none");
            } else {
                $("#lesson_vimeoUrl_last" + key).removeClass("d-none");
            }
        } else if (host_id == 5) {
            $("#lesson_iframeBox_last" + key).removeClass("d-none");
        } else if ([1, 6, 7, 8, 9, 10, 11, 12, 13].includes(parseInt(host_id))) {
            $("#lesson_fileupload_last" + key).removeClass("d-none");
        }
    }

    let host_id = $('#lesson_host_id_last').val();
    let key = $('#edit_lesson_id_last').val();
    let upload_type = $('#upload_type_last').val();
    if (key) {
        handleHostChange(key, host_id, upload_type);
    }

    $('.select_lesson_inside_host').change(function() {
        let key = $(this).data('key');
        let host_id = $(this).val();
        let upload_type = $('#upload_type_last').val();
        handleHostChange(key, host_id, upload_type);
    });


    // lesson section
    let lesson_id = $('#edit_lesson_id').val();
    let upload = $('#upload_type').val();

    if (lesson_id !='') {
        $('#lesson_section').show();
    }
    if (host_id == 1 || host_id == 6) {
        $("#lesson_fileupload").removeClass("d-none");
    } else if (host_id == 2 || host_id == 3) {
        $("#lesson_videoUrl").removeClass('d-none');
    } else if (host_id == 4) {
        if(upload_type=="Direct") {
            $("#lesson_fileupload").removeClass("d-none");
        } else {
            $("#lesson_vimeoUrl").removeClass('d-none');
        }
     
    } else if (host_id == 5) {
        $("#lesson_iframeBox").removeClass('d-none');
    }
    $('.select_host_id').change(function() {

        let category_id = $('.select_host_id').find(":selected").val();
        let upload_type = $('#upload_type').val();
        if (category_id == 2 || category_id == 3) {
            $("#lesson_iframeBox").addClass("d-none");
            $("#lesson_videoUrl").removeClass('d-none');
            $("#lesson_vimeoUrl").addClass("d-none");
            $("#lesson_vimeoVideo").val('');
            $("#lesson_youtubeVideo").val('');
            $("#lesson_fileupload").addClass("d-none");

        } else if ((category_id == 1) || (category_id == 6 ) || (category_id == 7 ) || (category_id == 8 ) || (category_id == 9 ) || (category_id == 10 ) || (category_id == 11 ) || (category_id == 12 ) || (category_id == 13 )) {

            $("#lesson_iframeBox").addClass("d-none");
            $("#lesson_fileupload").removeClass('d-none');
            $("#lesson_videoUrl").addClass("d-none");
            $("#lesson_vimeoUrl").addClass("d-none");
            $("#lesson_vimeoVideo").val('');
            $("#lesson_youtubeVideo").val('');

        } else if (category_id == 4) {
            $("#lesson_iframeBox").addClass("d-none");
            $("#lesson_videoUrl").addClass("d-none");
         
            $("#lesson_vimeoVideo").val('');
            $("#lesson_youtubeVideo").val('');                   
            if(upload_type=="Direct") {
                $("#lesson_fileupload").removeClass("d-none");
            } else {
                $("#lesson_vimeoUrl").removeClass('d-none');
            }
        } else if (category_id == 5) {
            $("#lesson_iframeBox").removeClass('d-none');
            $("#lesson_videoUrl").addClass("d-none");
            $("#lesson_vimeoUrl").addClass("d-none");
            $("#lesson_vimeoVideo").val('');
            $("#lesson_youtubeVideo").val('');
            $("#lesson_fileupload").addClass("d-none");
        } else {
            $("#lesson_iframeBox").addClass("d-none");
            $("#lesson_videoUrl").addClass("d-none");
            $("#lesson_vimeoUrl").addClass("d-none");
            $("#lesson_vimeoVideo").val('');
            $("#lesson_youtubeVideo").val('');
            $("#lesson_fileupload").addClass("d-none");
        }
    });

    // Quiz Outside quiz_section page
    hideOrShowQuizDivOutside('new'); 

    $(document).on('change', '.hideShowQuizTypOutside', function() {
        let type = $(this).data('type');
        hideOrShowQuizDivOutside(type);
    });

    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('.nav-link[data-tab="' + activeTab + '"]').tab('show');
    }

    $('.nav-link').on('click', function () {
        const selectedTab = $(this).data('tab');
        localStorage.setItem('activeTab', selectedTab);
    });

    // DetailView_Copy Page

    $('#select_input_type').change(function () {

        if ($(this).val() === '1') {
            $(".chapter_div").css("display", "block");
            $(".lesson_div").css("display", "none");
            $(".quiz_div").css("display", "none");

        } else if ($(this).val() === '2') {
            $(".chapter_div").css("display", "none");
            $(".lesson_div").css("display", "none");
            $(".quiz_div").css("display", "block");
        } else {
            $(".chapter_div").css("display", "none");
            $(".lesson_div").css("display", "block");
            $(".quiz_div").css("display", "none");
        }
    });

    $('#category_id').change(function () {
        let category_id = $('#category_id').find(":selected").val();

        if (category_id === 'Youtube' || category_id === 'URL') {
            $("#iframeBox").hide();
            $("#videoUrl").show();
            $("#vimeoUrl").hide();
            $("#vimeoVideo").val('');
            $("#youtubeVideo").val('');
            $("#fileupload").hide();

        } else if ((category_id === 'Self') || (category_id === 'Zip') || (category_id ===
            'PowerPoint') || (category_id === 'Excel') || (category_id === 'Text') || (
            category_id === 'Word') || (category_id === 'PDF') || (category_id === 'Image') || (
            category_id === 'AmazonS3') || (category_id === 'SCORM') || (category_id ===
            'SCORM-AwsS3')) {

            $("#iframeBox").hide();
            $("#fileupload").show();
            $("#videoUrl").hide();
            $("#vimeoUrl").hide();
            $("#vimeoVideo").val('');
            $("#youtubeVideo").val('');

        } else if (category_id === 'Vimeo') {
            $("#iframeBox").hide();
            $("#videoUrl").hide();
            $("#vimeoUrl").show();
            $("#vimeoVideo").val('');
            $("#youtubeVideo").val('');
            $("#fileupload").hide();
        } else if (category_id === 'Iframe') {
            $("#iframeBox").show();
            $("#videoUrl").hide();
            $("#vimeoUrl").hide();
            $("#vimeoVideo").val('');
            $("#youtubeVideo").val('');
            $("#fileupload").hide();
        } else {
            $("#iframeBox").hide();
            $("#videoUrl").hide();
            $("#vimeoUrl").hide();
            $("#vimeoVideo").val('');
            $("#youtubeVideo").val('');
            $("#fileupload").hide();
        }
    });

    $('#category_id1').change(function () {
        let category_id1 = $('#category_id1').find(":selected").val();
        if (category_id1 === 'Youtube') {
            $("#videoUrl1").show();
            $("#vimeoUrl1").hide();
            $("#vimeoVideo1").val('');
            $("#youtubeVideo1").val('');
            $("#fileupload1").hide();

        } else if ((category_id1 === 'Self') || (category_id === 'Document') || (category_id ===
            'Image') || (category_id1 === 'AmazonS3') || (category_id1 === 'SCORM') || (
            category_id1 === 'SCORM-AwsS3')) {
            $("#fileupload1").show();
            $("#videoUrl1").hide();
            $("#vimeoUrl1").hide();
            $("#vimeoVideo1").val('');
            $("#youtubeVideo1").val('');

        } else if (category_id1 === 'Vimeo') {
            $("#videoUrl1").hide();
            $("#vimeoUrl1").show();
            $("#vimeoVideo1").val('');
            $("#youtubeVideo1").val('');
            $("#fileupload1").hide();
        } else {
            $("#videoUrl1").hide();
            $("#vimeoUrl1").hide();
            $("#vimeoVideo1").val('');
            $("#youtubeVideo1").val('');
            $("#fileupload1").hide();
        }
    });

    // index page
    $("#table_id").on("change",'.course_status_switch_btn', function() {
        var id = $(this).data("id");
        
        if ($(this).is(":checked")) {
            var status = "1";
        } else {
            var status = "0";
        }
        
        var url = $("#url").val();
        
        $.ajax({
            type: "POST",
            data: {'status': status, 'id': id},
            dataType: "json",
            url: url + "/" + "lms/course/switch",
            success: function(data) {                       
                setTimeout(function() {
                    toastr.success(data.message, "Success", {
                        timeOut: 5000,
                    });
                }, 500);
            },
            error: function(data) {
                
                setTimeout(function() {
                    toastr.error(data.error, "Error", {
                        timeOut: 5000,
                    });
                }, 500);
            },
        });
    });

    $('.full').on('click', function() {
        let rating = $(this).data('rating');           
        $('#review_rating').val(rating);
        $('#reviewSubmit').removeAttr("disabled");
    })
    
    $('.lessonCompleteModal').on('click', function() {
        let lesson_id = $(this).data('lesson_id');           
        let is_complete = $(this).data('is_complete');           
       
        if (is_complete==0) {
            $('#complete-word').html('Are You Want To Sure to Complete');
        } else {
            $('#complete-word').html('Are You Want To Sure to Incomplete');
        }
        $('#is_complete').val(is_complete);
        $('#lesson_complete_id').val(lesson_id);
        $('#lessonModal').modal('toggle');           
    })

    var data = $("#select_host").val();
    if(data == "Self"){
            $("#file_upload").css("display", "block");
            $("#url_field").css("display", "none");
            }
    else{
            $("#file_upload").css("display", "none");
            $("#url_field").css("display", "block");   
    }

    $("#select_host").on("change", function() {
        var selected = $(this).val();
        if (selected == "Self"){
                $("#file_upload").css("display", "block");
                $("#url_field").css("display", "none");
        } else{
            $("#file_upload").css("display", "none");
            $("#url_field").css("display", "block"); 
        }
    });

    // level page index
    $('.subcategory').on({
        mouseenter: function() {
            let id = $(this).data('sub_cat_id');
            $('#editbtn_' + id).removeClass('d-none');
            $('#deletebtn_' + id).removeClass('d-none');
        },
        mouseleave: function() {
            let id = $(this).data('sub_cat_id');
            $('#editbtn_' + id).addClass('d-none');
            $('#deletebtn_' + id).addClass('d-none');
        },

    });
    $('.deleteCategory').on('click', function() {
        var url = $("#url").val();
        let id = $(this).data('id');
        let delete_url = url + "/" + "lms/course-level/delete/" + id;
        $('#deleteStudentTypeModal').modal('show');
        $("#delurl").attr("href", delete_url);
    })


    let host_id_parent = $('#course_host_id').val();
    let upload_parent = $('#upload_type').val();
    if(host_id_parent==1 || host_id_parent==6 || host_id_parent==7 || host_id_parent==8 || host_id_parent==9 || host_id_parent==10 || host_id_parent==11 || host_id_parent==12 || host_id_parent==13) {
        $("#edit_fileupload").removeClass("d-none");
    } else if (host_id_parent==2 || host_id_parent==3) {
        $("#edit_videoUrl").removeClass('d-none');
    } else if (host_id_parent==4) {

        if(upload_parent=="Direct") {
            $("#edit_fileupload").removeClass("d-none");
        } else {
            $("#edit_vimeoUrl").removeClass('d-none');
        }
    } else if (host_id_parent==5) {
        $("#edit_iframeBox").removeClass('d-none');
    }
    $('.edit_select_host_id').change(function() {
        $("#edit_fileupload").addClass("d-none");
        let category_id = $('.edit_select_host_id').val();
        let upload_type = $('#upload_type').val();
        if (category_id == 2 || category_id == 3) {
            $("#edit_iframeBox").addClass("d-none");
            $("#edit_videoUrl").removeClass('d-none');
            $("#edit_vimeoUrl").addClass("d-none");
            $("#edit_vimeoVideo").val('');
            $("#edit_youtubeVideo").val('');
            $("#edit_fileupload").addClass("d-none");

        } else if ((category_id == 1) || category_id == 6 || (category_id == 7) || (category_id ==8
        ) || (category_id ==9) || (category_id ==10) || (
            category_id ==11) || (category_id ==12) || (category_id ==13)) {

            $("#edit_iframeBox").addClass("d-none");
            $("#edit_fileupload").removeClass('d-none');
            $("#edit_videoUrl").addClass("d-none");
            $("#edit_vimeoUrl").addClass("d-none");
            $("#edit_vimeoVideo").val('');
            $("#edit_youtubeVideo").val('');

        } else if (category_id == 4) {
            $("#edit_iframeBox").addClass("d-none");
            $("#edit_videoUrl").addClass("d-none");

            $("#edit_vimeoVideo").val('');
            if(upload_type=="Direct") {
                $("#edit_fileupload").removeClass("d-none");
            } else {
                $("#edit_vimeoUrl").removeClass('d-none');
            }
            $("#edit_youtubeVideo").val('');

        } else if (category_id == 5) {
            $("#edit_iframeBox").removeClass('d-none');
            $("#edit_videoUrl").addClass("d-none");
            $("#edit_vimeoUrl").addClass("d-none");
            $("#edit_vimeoVideo").val('');
            $("#edit_youtubeVideo").val('');
            $("#edit_fileupload").addClass("d-none");
        } else {
            $("#edit_iframeBox").addClass("d-none");
            $("#edit_videoUrl").addClass("d-none");
            $("#edit_vimeoUrl").addClass("d-none");
            $("#edit_vimeoVideo").val('');
            $("#edit_youtubeVideo").val('');
            $("#edit_fileupload").addClass("d-none");
        }
    });

    // parent course view page
    $('.deleteReview').on('click', function () {
        var url = $("#url").val();
        let id = $(this).data('id');
        let course_id = $(this).data('course_id');
        let delete_url = url + "/lms/" + "review/delete/" + id + "/" + course_id;
        $('#deleteReviewModal').modal('show');
        $("#reviewdelurl").attr("href", delete_url);
    })

    // question_bank page
    $('.multiple_images input[type="file"]').change(function() { 
        $(this).closest('.multiple_images').find('.show_file_name').html('File Selected');
        
    });

    $("#admin_commission").on("input", function() {
        var admin_com = $(this).val();
        if(admin_com <= 100 ){
            $('[name=teacher_commission]').val(100 - admin_com);
        }else{
            $('[name=admin_commission]').val(100);
            toastr.warning('Maximum Value 100', 'Warnning', {
            timeOut: 5000
            })
        }
    });
});

if($('.course_switch_btn').length){
    document.addEventListener('DOMContentLoaded', function () {
        const isFreeCheckbox = document.querySelector('.course_switch_btn');
        const courseFee = document.getElementById('courseFee');
        const courseFeeDiscount = document.getElementById('courseFeeDiscount');
        const priceInput = document.getElementById('price');
        const discountPriceInput = document.getElementById('discount_price');
    
        function toggleCourseFee() {
            if (isFreeCheckbox.checked) {
                courseFee.style.display = 'none';
                courseFeeDiscount.style.display = 'none';
                priceInput.setAttribute('disabled', 'disabled');
                discountPriceInput.setAttribute('disabled', 'disabled');
                priceInput.value = 0;
                discountPriceInput.value = 0;
            } else {
                courseFee.style.display = 'block';
                courseFeeDiscount.style.display = 'block';
                priceInput.removeAttribute('disabled');
                discountPriceInput.removeAttribute('disabled');
            }
        }
    
        toggleCourseFee();
    
        isFreeCheckbox.addEventListener('change', toggleCourseFee);
    
    
        const discountInput = document.getElementById('discount_price');
    
        discountInput.addEventListener('input', function () {
            const priceValue = parseFloat(priceInput.value) || 0;
            const discountValue = parseFloat(discountInput.value) || 0;
    
            if (discountValue > priceValue) {
                alert("Discount price cannot be greater than the original price.");
                discountInput.value = priceValue;
            }
        });
    
        
    });

}


function lmsSummernoteImageUpload(files, editor) {
    var url = $("#url").val();
    var formData = new FormData();

    $.each(files, function(i, file) {
        formData.append("files[" + i + "]", file);
    });

    $.ajax({
        url: url + '/editor/upload-file',
        type: 'post',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function(response) {
            $.each(response, function(i, imageUrl) {
                editor.summernote('insertImage', imageUrl);
            });
        },
        error: function(data) {
            if (typeof toastr !== 'undefined') {
                if (data.status === 404) {
                    toastr.error("What you are looking is not found", 'Opps!');
                    return;
                } else if (data.status === 500) {
                    toastr.error('Something went wrong. If you are seeing this message multiple times, please contact Spondon It author.', 'Opps');
                    return;
                }

                toastr.error('Image upload failed.', 'Error');
            }
        }
    });
}

function lmsSummernoteOptions() {
    return {
        height: 200,
        tabsize: 2,
        callbacks: {
            onImageUpload: function(files) {
                lmsSummernoteImageUpload(files, $(this));
            }
        },
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    };
}

if($('.lms_summernote_edit').length){
    $('.lms_summernote_edit').summernote(lmsSummernoteOptions());

}


function toggleDripFields(type, index) {
    const dateField = document.getElementById('lesson_date_' + index);
    const dayField = document.getElementById('lesson_day_' + index);

    if (type === 'date') {
        dateField.style.display = 'block';
        dayField.style.display = 'none';
    } else if (type === 'days') {
        dateField.style.display = 'none';
        dayField.style.display = 'block';
    }
}

// Homework Tab List

$('.evalutionHomework').on('click', function(e) {
    e.preventDefault();
    $('#evalutionModal').modal('show');
    let url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "get",
        success: function(response) {
            $('#evalutionModal .modal-content').html(response);
        },
    });
});

$(document).on("shown.bs.modal", function () {
    var fileInput = document.getElementById("question_upload_content_file_st_mat_tab_main");
    var placeholderInput = document.getElementById("question_placeholderUploadContentStMatTabMain");

    if (fileInput) {
        fileInput.addEventListener("change", function (event) {
            var fileName = event.target.files[0]?.name || "";
            if (placeholderInput) {
                placeholderInput.setAttribute("value", fileName);
            }
        });
    } else {
        console.log("File input not found!");
    }
});

//chapter list main

$('.add_question').click(function(e) {
    e.preventDefault();
    var lesson_id = $(this).data('lesson_id');
    var chapter_id = $(this).data('chapter_id');
    $('#add_question_section_inside' + chapter_id).show();
    $('#quiz_id_inside' + chapter_id).val(lesson_id);

});
$('.add_online_question').click(function(e) {
    e.preventDefault();
    var lesson_id = $(this).data('lesson_id');
    var chapter_id = $(this).data('chapter_id');
    $('#add_online_question_section_inside_' + lesson_id).show();
    $('#add_online_chapter_id').val(chapter_id);
    $('#add_online_lesson_id').val(lesson_id);

});
$('.close_question_section').click(function() {
    var chapter_id = $(this).data('chapter_id');
    $('#add_question_section_inside' + chapter_id).hide();
})

let lms_option_list = $('.lms_option_list');
let minus_option_box = $('#minus_option_box');
let add_option_box = $('#add_option_box');
let chapter_section = $('#chapter_section');
let lesson_section = $('#lesson_section');
let onlineexam_section = $('#onlineexam_section');
let virtualclass_section = $('#virtualclass_section');
let studymaterial_section = $('#studymaterial_section');
let quiz_section = $('#quiz_section');
let homework_section = $('#homework_section');

$('.add_option_box').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#lms_option_list' + chapter_id).show();
    $('#minus_option_box' + chapter_id).show();
    $('#add_option_box' + chapter_id).hide();
    $('#onlineexam_section' + chapter_id).hide();

    var fileInput = document.getElementById("document_file_lesson" + chapter_id);
    if (fileInput) {
        fileInput.addEventListener("change", showFileName);

        function showFileName(event) {
            var fileInput = event.srcElement;
            var fileName = fileInput.files[0].name;
            document.getElementById("placeholderFileLesson" + chapter_id).placeholder = fileName;
        }
    }
});

$('.minus_option_box').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).hide();
    $('#lesson_section' + chapter_id).hide();
    $('#onlineexam_section' + chapter_id).hide();
    $('#virtualclass_section' + chapter_id).hide();
    $('#studymaterial_section' + chapter_id).hide();
    $('#homework' + chapter_id).hide();

    $('#quiz_section' + chapter_id).hide();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).show();
    $('#minus_option_box' + chapter_id).hide();
});

$(document).ready(function() {
    $('#lms_option_list').hide();
})

$('#add_option_box').click(function() {
    lms_option_list.show();
    minus_option_box.show();
    add_option_box.hide();
})

$('#minus_option_box').click(function() {
    lms_option_list.hide();
    minus_option_box.hide();
    lesson_section.hide();
    quiz_section.hide();
    virtualclass_section.hide();
    studymaterial_section.hide();
    chapter_section.hide();
    onlineexam_section.hide();
    add_option_box.show();
    homework_section.hide();
})

$('#show_chapter_section').click(function() {
    lms_option_list.hide();
    lesson_section.hide();
    quiz_section.hide();
    onlineexam_section.hide();
    virtualclass_section.hide();
    studymaterial_section.hide();
    chapter_section.show();
    homework_section.hide();
})

$('#show_lesson_section').click(function() {
    lms_option_list.hide();
    lesson_section.show();
    quiz_section.hide();
    chapter_section.hide();
    onlineexam_section.hide();
    virtualclass_section.hide();
    studymaterial_section.hide();
    homework_section.hide();
})

$('#show_onlineexam_section').click(function() {
    lms_option_list.hide();
    lesson_section.hide();
    quiz_section.hide();
    chapter_section.hide();
    onlineexam_section.show();
    virtualclass_section.hide();
    studymaterial_section.hide();
    homework_section.hide();
})

$('#show_virtualclass_section').click(function() {
    lms_option_list.hide();
    lesson_section.hide();
    quiz_section.hide();
    chapter_section.hide();
    studymaterial_section.hide();
    virtualclass_section.show();
})

$('#show_studymaterial_section').click(function() {
    lms_option_list.hide();
    lesson_section.hide();
    quiz_section.hide();
    chapter_section.hide();
    onlineexam_section.hide();
    studymaterial_section.show();
})

$('#show_quiz_section').click(function() {
    lms_option_list.hide();
    lesson_section.hide();
    quiz_section.show();
    chapter_section.hide();
    onlineexam_section.hide();
    virtualclass_section.hide();
    studymaterial_section.hide();
    homework_section.hide();
})

$('#show_homework_section').click(function() {
    lms_option_list.hide();
    lesson_section.hide();
    quiz_section.hide();
    chapter_section.hide();
    onlineexam_section.hide();
    virtualclass_section.hide();
    studymaterial_section.hide();
    homework_section.show();
})

$('.show_chapter_section_inside').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).show();
    $('#lesson_section' + chapter_id).hide();
    $('#quiz_section' + chapter_id).hide();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).hide();
    $('#minus_option_box' + chapter_id).show();
    $('#onlineexam_section' + chapter_id).hide();
    $('#homework' + chapter_id).hide();

})

$('.show_lesson_section_inside').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).hide();
    $('#lesson_section' + chapter_id).show();
    $('#quiz_section' + chapter_id).hide();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).hide();
    $('#minus_option_box' + chapter_id).show();
    $('#onlineexam_section' + chapter_id).hide();
    $('#homework' + chapter_id).hide();

})

$('.show_homework_inside').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).hide();
    $('#lesson_section' + chapter_id).hide();
    $('#quiz_section' + chapter_id).hide();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).hide();
    $('#minus_option_box' + chapter_id).show();
    $('#onlineexam_section' + chapter_id).hide();
    $('#homework' + chapter_id).show();
})

$('.show_onlineexam_section_inside').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).hide();
    $('#onlineexam_section' + chapter_id).show();
    $('#lesson_section' + chapter_id).hide();
    $('#quiz_section' + chapter_id).hide();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).hide();
    $('#minus_option_box' + chapter_id).show();
    $('#homework' + chapter_id).hide();
})

$('.show_virtualclass_section_inside').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).hide();
    $('#onlineexam_section' + chapter_id).hide();
    $('#virtualclass_section' + chapter_id).show();
    $('#lesson_section' + chapter_id).hide();
    $('#quiz_section' + chapter_id).hide();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).hide();
    $('#minus_option_box' + chapter_id).show();
})

$('.show_studymaterial_section_inside').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).hide();
    $('#onlineexam_section' + chapter_id).hide();
    $('#studymaterial_section' + chapter_id).show();
    $('#lesson_section' + chapter_id).hide();
    $('#quiz_section' + chapter_id).hide();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).hide();
    $('#minus_option_box' + chapter_id).show();
})

$('.show_quiz_section_inside').click(function() {
    var chapter_id = $(this).data('chapter');
    $('#chapter_section' + chapter_id).hide();
    $('#lesson_section' + chapter_id).hide();
    $('#quiz_section' + chapter_id).show();
    $('#lms_option_list' + chapter_id).hide();
    $('#add_option_box' + chapter_id).hide();
    $('#minus_option_box' + chapter_id).show();
    $('#onlineexam_section' + chapter_id).hide();
    $('#homework' + chapter_id).hide();
})

$('.permission-checkAll').on('click', function() {
    if ($(this).is(":checked")) {
        $('.module_id_' + $(this).val()).each(function() {
            $(this).prop('checked', true);
        });
    } else {
        $('.module_id_' + $(this).val()).each(function() {
            $(this).prop('checked', false);
        });
    }
});

$('.module_link').on('click', function() {
    var module_id = $(this).parents('.single_permission').attr("id");
    var module_link_id = $(this).val();
    if ($(this).is(":checked")) {
        $(".module_option_" + module_id + '_' + module_link_id).prop('checked', true);
    } else {
        $(".module_option_" + module_id + '_' + module_link_id).prop('checked', false);
    }
    var checked = 0;
    $('.module_id_' + module_id).each(function() {
        if ($(this).is(":checked")) {
            checked++;
        }
    });

    if (checked > 0) {
        $(".main_module_id_" + module_id).prop('checked', true);
    } else {
        $(".main_module_id_" + module_id).prop('checked', false);
    }
});

$('.module_link_option').on('click', function() {
    var module_id = $(this).parents('.single_permission').attr("id");
    var module_link = $(this).parents('.module_link_option_div').attr("id");
    
    var link_checked = 0;
    $('.module_option_' + module_id + '_' + module_link).each(function() {
        if ($(this).is(":checked")) {
            link_checked++;
        }
    });

    if (link_checked > 0) {
        $("#Sub_Module_" + module_link).prop('checked', true);
    } else {
        $("#Sub_Module_" + module_link).prop('checked', false);
    }
    
    var checked = 0;
    $('.module_id_' + module_id).each(function() {
        if ($(this).is(":checked")) {
            checked++;
        }
    });

    if (checked > 0) {
        $(".main_module_id_" + module_id).prop('checked', true);
    } else {
        $(".main_module_id_" + module_id).prop('checked', false);
    }
});

$(document).on("click", "#create-option", function(event) {
    $('#multiple-options' + chapter_id).html('');
    var chapter_id = $(this).data('chapter_id');
    var number_of_option = $('#number_of_option' + chapter_id).val();
    for (var i = 1; i <= number_of_option; i++) {
        var appendRow = '';
        appendRow += "<div class='row  mt-25'>";
        appendRow += "<div class='col-lg-10'>";
        appendRow += "<div class='input-effect'>"
        appendRow += "<input class='primary_input_field name' placeholder='option " + i +
            "' type='text' name='option[]' autocomplete='off' required>";
        appendRow += "</div>";
        appendRow += "</div>";
        appendRow += "<div class='col-lg-2 mt-15'>";


        appendRow += "<label class='primary_checkbox d-flex mr-12 ' for='option_check_" + i + "'>";

        appendRow += "<input type='checkbox'  id='option_check_" + i + "' name='option_check_" + i +
            "' value='1'> <span class='checkmark'></span>";
        appendRow += "</label>";
        appendRow += "</div>";
        appendRow += "</div>";

        $("#multiple-options" + chapter_id).append(appendRow);
    }
});

function examDelete(id) {
    var modal = $('#deleteOnlineExam');
    modal.find('input[name=meeting_id]').val(id)
    modal.modal('show');
}

function editOnlineExam(id) {
    $('#edit_online_exam_' + id).removeClass('d-none');
}

function closeOnlineExam(id) {
    $('#edit_online_exam_' + id).addClass('d-none');
}

function quizDelete(id) {
    var modal = $('#deleteOnlineQuiz');
    modal.find('input[name=quiz_id]').val(id)
    modal.modal('show');
}

function editQuiz(id) {
    $('#edit_quiz_' + id).removeClass('d-none');
}

function closeQuiz(id) {
    $('#edit_quiz_' + id).addClass('d-none');
}

function meetingDelete(id) {
    var modal = $('#meetingDelete');
    modal.find('input[name=meeting_id]').val(id)
    modal.modal('show');
}

function editMeeting(id) {
    $('#edit_meeting_' + id).removeClass('d-none');
}

function closeMeeting(id) {
    $('#edit_meeting_' + id).addClass('d-none');
}

function studymaterialDelete(id) {
    var modal = $('#StudymaterialDelete');
    modal.find('input[name=id]').val(id)
    modal.modal('show');
}

function editStudymaterial(id) {
    $('#edit_studymaterial_' + id).removeClass('d-none');
}

function closeStudymaterial(id) {
    $('#edit_studymaterial_' + id).addClass('d-none');
}

function editChapter(id) {
    $('#edit_chapter_' + id).removeClass('d-none');
}

function closeChapter(id) {
    $('#edit_chapter_' + id).addClass('d-none');
}

function editLesson(id) {
    $('#edit_lesson_' + id).removeClass('d-none');
}

function closeLesson(id) {
    $('#edit_lesson_' + id).addClass('d-none');
}

function editHomework(id) {
    $('#edit_homework_' + id).removeClass('d-none');
}

function closeHomework(id) {
    $('#edit_homework_' + id).addClass('d-none');
}

function homeworkDelete(id) {
    var modal = $('#deleteHw');
    modal.find('input[name=homework_id]').val(id)
    modal.modal('show');
}

function getFileName(value, placeholder) {
    "use strict";
    if (value) {
        var startIndex = (value.indexOf('\\') >= 0 ? value.lastIndexOf('\\') : value.lastIndexOf('/'));
        var filename = value.substring(startIndex);
        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
            filename = filename.substring(1);
        }
        $(placeholder).attr('placeholder', '');
        $(placeholder).attr('placeholder', filename);
    }
}

function closeQuizQuestion() {
    $('.edit_quiz_data').addClass('d-none');
}

// edit_lesson page
$('.select_lesson_inside_host').change(function() {

    let key = $(this).data('lessonval');
    if(typeof key == 'undefined'){
        key = $(this).data('key');
    }
    let category_id = $(this).val();
    if (category_id == 2 || category_id == 3) {
        $("#lesson_iframeBox"+key).addClass("d-none");
        $("#lesson_videoUrl"+key).removeClass('d-none');
        $("#lesson_vimeoUrl"+key).addClass("d-none");
        $("#lesson_vimeoVideo"+key).val('');
        $("#lesson_youtubeVideo"+key).val('');
        $("#lesson_fileupload"+key).addClass("d-none");

    } else if ((category_id == 1) || (category_id == 6 ) || (category_id == 7 ) || (category_id == 8 ) || (category_id == 9 ) || (category_id == 10 ) || (category_id == 11 ) || (category_id == 12 ) || (category_id == 13 )) {

        $("#lesson_iframeBox"+key).addClass("d-none");
        $("#lesson_fileupload"+key).removeClass('d-none');
        $("#lesson_videoUrl"+key).addClass("d-none");
        $("#lesson_vimeoUrl"+key).addClass("d-none");
        $("#lesson_vimeoVideo"+key).val('');
        $("#lesson_youtubeVideo"+key).val('');

    } else if (category_id == 4) {
        $("#lesson_iframeBox"+key).addClass("d-none");
        $("#lesson_videoUrl"+key).addClass("d-none");
        $("#lesson_youtubeVideo"+key).val('');
        if(upload_type=="Direct") {
            $("#lesson_fileupload"+key).removeClass("d-none");
            $("#lesson_vimeoUrl"+key).addClass('d-none');
        } else {
            $("#lesson_fileupload"+key).addClass("d-none");
            $("#lesson_vimeoUrl"+key).removeClass('d-none');
        }
    } else if (category_id == 5) {
        $("#lesson_iframeBox"+key).removeClass('d-none');
        $("#lesson_videoUrl"+key).addClass("d-none");
        $("#lesson_vimeoUrl"+key).addClass("d-none");
        $("#lesson_vimeoVideo"+key).val('');
        $("#lesson_youtubeVideo"+key).val('');
        $("#lesson_fileupload"+key).addClass("d-none");
    } else {
        $("#lesson_iframeBox"+key).addClass("d-none");
        $("#lesson_videoUrl"+key).addClass("d-none");
        $("#lesson_vimeoUrl"+key).addClass("d-none");
        $("#lesson_vimeoVideo"+key).val('');
        $("#lesson_youtubeVideo"+key).val('');
        $("#lesson_fileupload"+key).addClass("d-none");
    }
});

// edit_quiz page

$('.isLesson').on('change', function() {
    let divId = $(this).data('id');

    if ($(this).is(":checked")) {
        $('#lesson_div_'+divId).removeClass('d-none');
    } else {
        $('#lesson_div_'+divId).addClass('d-none');
    }
})
$(document).on('change', '.hideShowQuizType', function() {
    let type = $(this).data('type');
    let divId = $(this).data('id');

    hideOrShowQuizDiv(type, divId);
})

$(document).on('change', '.hideShowQuizTypeInside', function() {
    let type = $(this).data('type');
    let divId = $(this).data('id');

    hideOrShowQuizDiv(type, divId);
})

function hideOrShowQuizDiv(type, divId) {
    if (type == "new") {
        $('#new_content_'+divId).removeClass('d-none');         
        $('#quiz_div_'+divId).addClass('d-none');         
    } else if(type == "exit") {
        $('#new_content_'+divId).addClass('d-none');
        $('#quiz_div_'+divId).removeClass('d-none');         

    } else {
        $('#new_content_'+divId).removeClass('d-none');         
        $('#quiz_div_'+divId).addClass('d-none');  
    }
}

// quiz_outside quiz_section page
function hideOrShowQuizDivOutside(type) {
    if (type === "new") {
        $('#new_content_outside').removeClass('d-none');  
        $('#quiz_div_outside').addClass('d-none');        
    } else if (type === "exit") {
        $('#new_content_outside').addClass('d-none');     
        $('#quiz_div_outside').removeClass('d-none');     
    }
}

if($('.lms_summernote_create').length){
    $('.lms_summernote_create').summernote(lmsSummernoteOptions());

}


$('.nastable').sortable({
    cursor: "move",
    connectWith: [".nastable", ".nastable2"],
    handle: '.nestable_handle',
    update: function (event, ui) {
        let ids = $(this).sortable('toArray', {
            attribute: 'data-id'
        });

        if (ids.length > 0) {
            let data = {
                '_token': '{{ csrf_token() }}',
                'ids': ids,
            }
            $.get("{{ route('lms.changeChapterPosition') }}", data, function (data) {

            });
        }
    }
}).disableSelection();

$('.nastable2').sortable({
    cursor: "move",
    connectWith: ".nastable2",
    handle: '.nestable_handle',
    update: function (event, ui) {
        let ids = $(this).sortable('toArray', {
            attribute: 'data-id'
        });
        if (ids.length > 0) {
            let data = {
                '_token': '{{ csrf_token() }}',
                'ids': ids,
            }
            $.post("{{ route('lms.changeLessonPosition') }}", data, function (data) {

            });
        }
        ordering();
    },
    receive: function (event, ui) {
        var chapter_id = event.target.attributes[1].value;
        var lesson = ui.item[0].attributes[1].value;


        let data = {
            'chapter_id': chapter_id,
            'lesson_id': lesson,
            '_token': '{{ csrf_token() }}'
        }
        $.post("{{ route('lms.changeLessonChapter') }}", data, function (data) {

        });
    }
}).disableSelection();

function ordering() {
    var chepters = $('.nastable2');
    chepters.each(function () {
        var childs = $(this).find(".serial");
        childs.each(function (k, v) {
            $(this).html(k + 1);
        });
    });
}

// DetailView_Copy Page
$(".type1").on("click", function () {
    if ($('.type1').is(':checked')) {
        $(".courseBox").show();
        $(".quizBox").hide();
        $(".dripCheck").show();
        $("#quiz_id").val('');
        $(".makeResize").addClass("col-xl-4");
        $(".makeResize").removeClass("col-xl-6");
    }
});

$(".type2").on("click", function () {
    if ($('.type2').is(':checked')) {
        $(".courseBox").hide();
        $(".quizBox").show();
        $(".dripCheck").hide();

        $(".makeResize").addClass("col-xl-6");
        $(".makeResize").removeClass("col-xl-4");
    }
});

$(document).on('click', '.fileEditFrom', function () {

    let file = $(this).data('item');
    var IdElement = $('.editFileId');
    var NameFileElement = $('.editFileName');
    var PrivacyElement = $('.editFilePrivacy');
    var StatusElement = $('.editFileStatus');
    IdElement.val(file.id);
    NameFileElement.val(file.fileName);
    PrivacyElement.val(file.lock);
    StatusElement.val(file.status);

    PrivacyElement.niceSelect('update');
    StatusElement.niceSelect('update');
})

function getFileName(value, placeholder) {
    if (value) {
        var startIndex = (value.indexOf('\\') >= 0 ? value.lastIndexOf('\\') : value.lastIndexOf('/'));
        var filename = value.substring(startIndex);
        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
            filename = filename.substring(1);
        }
        $(placeholder).attr('placeholder', '');
        $(placeholder).attr('placeholder', filename);
    }
}

$(".toggler_lines").on("click", function() {
    $(this).closest(".video_palyer_lists").toggleClass("active");
});


// question_bank page
function uploadImage(id) {
    $('.show_file_name'+id).html('File Selected');
    var select_image= $('#question_image'+id);

    var file = document.getElementById("question_image"+id).files[0];
    if (file) {
        if (file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/jpg") {
            var img = new Image();

            img.src = window.URL.createObjectURL(file);

            img.onload = function() {
                var width = img.naturalWidth,
                    height = img.naturalHeight;
                window.URL.revokeObjectURL(img.src);
                if (width <= 650 && height <= 450) {
                    $('.show_file_name'+id).html(file.name.substr(0, 10));
                } else {
                    $('.show_file_name'+id).html("Invalid image dimension");
                    $('#question_image'+id).val(null);
                }
            };
        } else {
            $('.show_file_name'+id).html("Invalid file type");
            $('#question_image'+id).val(null);
        }
    }
}

$('#question_bank_submit').click(function(e){
    e.preventDefault();
    var ck_box = $('.multiple-images input[type="checkbox"]:checked').length;
    var answer_type = $("#answer_type").val();
    var question_type = $("#question-type").val();

    if(ck_box > 0){
            if($("input[name='images[]']" ).val()=="")
            { 
                toastr.warning('Please Select Valid Option Images', 'Warning', {
                            timeOut: 5000 })
            }else{
                if (answer_type=='radio' && ck_box >1 ) {
                    toastr.warning('Please Select One Correct Answer', 'Warning', {
                            timeOut: 5000
                        })
                } else {
                    $('#question_bank').submit();
                }
                
            }
    } else {
        
        if (question_type!='MI') {
            $('#question_bank').submit();
        }else{
            toastr.warning('Please Select Correct  Answer', 'Warning', {
                timeOut: 5000
            })
        }
    } 
});

$(document).on('click', '.common-checkbox', function() {
    var answer_type = $("#answer_type").val();
    
    if (answer_type=='radio') {
        $('.common-checkbox').prop('checked', false);
        $(this).prop('checked', true)
    }
})




// new_enroll page
$("#select_semester_label").on("change", function() {

    
    var url = $("#url").val();
    var i = 0;
    let semester_id = $(this).val();
    let academic_id = $('#select_academic').val();
    let session_id = $('#select_session').val();
    let faculty_id = $('#select_faculty').val();
    let department_id = $('#select_dept').val();
    let un_semester_label_id = $('#select_semester_label').val();

    if (session_id =='') {
        setTimeout(function() {
            toastr.error(
                "Session Not Found",
                "Error ",{
                    timeOut: 5000,
                });}, 500);

        $("#select_semester option:selected").prop("selected", false)
        return ;
    }
    if (department_id =='') {
        setTimeout(function() {
            toastr.error(
                "Department Not Found",
                "Error ",{
                    timeOut: 5000,
                });}, 500);
        $("#select_semester option:selected").prop("selected", false)

        return ;
    }
    if (semester_id =='') {
        setTimeout(function() {
            toastr.error(
                "Semester Not Found",
                "Error ",{
                    timeOut: 5000,
                });}, 500);
        $("#select_semester option:selected").prop("selected", false)

        return ;
    }
    if (academic_id =='') {
        setTimeout(function() {
            toastr.error(
                "Academic Not Found",
                "Error ",{
                    timeOut: 5000,
                });}, 500);
        return ;
    }
    if (un_semester_label_id =='') {
        setTimeout(function() {
            toastr.error(
                "Semester Label Not Found",
                "Error ",{
                    timeOut: 5000,
                });}, 500);
        return ;
    }

    var formData = {
        semester_id : semester_id,
        academic_id : academic_id,
        session_id : session_id,
        faculty_id : faculty_id,
        department_id : department_id,
        un_semester_label_id : un_semester_label_id,
    };

    $.ajax({
        type: "GET",
        data: formData,
        dataType: "json",
        url: url + "/university/" + "get-university-wise-student",
        beforeSend: function() {
            $('#select_un_student_loader').addClass('pre_loader').removeClass('loader');
        },
        success: function(data) {
            var a = "";
            $.each(data, function(i, item) {
                if (item.length) {
                    $("#select_un_student").find("option").not(":first").remove();
                    $("#select_un_student_div ul").find("li").not(":first").remove();

                    $.each(item, function(i, students) {
                        $("#select_un_student").append(
                            $("<option>", {
                                value: students.student.id,
                                text: students.student.full_name,
                            })
                        );

                        $("#select_un_student_div ul").append(
                            "<li data-value='" +
                            students.student.id +
                            "' class='option'>" +
                            students.student.full_name +
                            "</li>"
                        );
                    });
                    $("#select_un_student").multiselect('reset');
                } else {
                    $("#select_un_student").find("option").not(":first").remove();
                    $("#select_un_student_div ul").find("li").not(":first").remove();
                }
            });
        },
        error: function(data) {
            console.log("Error:", data);
        },
        complete: function() {
            i--;
            if (i <= 0) {
                $('#select_un_student_loader').removeClass('pre_loader').addClass('loader');
            }
        }
    });
});

function rejectPayment(id) {
    var modal = $('#rejectPaymentModal');
    modal.find('#showId').val(id)
    modal.modal('show');
}

function viewReason(id) {
    var reason = $('.reason' + id).data('reason');
    var modal = $('#showReasonModal');
    modal.find('textarea').val(reason)
    modal.modal('show');
}
