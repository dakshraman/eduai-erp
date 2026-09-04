

function showHideQuestionType(question_type) {
    "use strict"
    $('#type_name_show').text($("#question-type option:selected").text());
    $('.question_bank_div').hide();
    question_type = question_type == 'SA' ?  'F' : question_type;
    if(question_type){
        $("[data-type='"+question_type+"']").show();
    }

}


function submit_exam(){
    "use strict"
    let duration_type = $('#duration_type').val();

    if (duration_type == 'question'){
        let current_page = parseInt($('#current_page').val());
        let last_page = parseInt($('#total_questions').val());
        if(current_page == last_page){
            $('#online_exam_form').submit();
        } else{
            window.location.href =replaceUrlParam(window.location.href, 'page', current_page + 1)
        }
    } else{
        $('#online_exam_form').submit();
    }
}


    
function replaceUrlParam(url, paramName, paramValue)
{
    "use strict"

    if (paramValue == null) {
        paramValue = '';
    }
    var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
    if (url.search(pattern)>=0) {
        return url.replace(pattern,'$1' + paramValue + '$2');
    }
    url = url.replace(/[?#]$/,'');
    return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
}
function timeCalculation(last_entry_time) {    
    "use strict"

    var countEndTime = new Date(last_entry_time).getTime();
    let left_time = moment(last_entry_time).diff(moment(), 'seconds');

    if (left_time <= 0 && $('#OMcountDownTimer').length) {
        document.getElementById("OMcountDownTimer").innerHTML =
            "<span class='text-white'>Exam submittion time expired</span>";
        submit_exam();
    } else {
       
        var currentTime = setInterval(function () {
          
            var countStartTime = new Date().getTime();

           
            var distance = countEndTime - countStartTime;

           
            var days = Math.floor(distance / (1000 * 3600 * 24));
          
            var hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

           
            if ($("#OMcountDownTimer").length) {
                document.getElementById("OMcountDownTimer").innerHTML =
                    "<strong class='pr-1'>Remaining Time : </strong><span class='pl-1'>" +
                    " " +
                    days +
                    " days " +
                    hours +
                    " hours " +
                    minutes +
                    " min " +
                    seconds +
                    " sec </span>";
            }

          
            if (distance < 0) {
                clearInterval(currentTime);
                document.getElementById("OMcountDownTimer").innerHTML =
                    "<span class='text-white'>Exam submittion time expired</span>";
              

                submit_exam();
            }
        }, 1000);
    }
}

(function () {
    "use strict"
    
    $(document).ready(function () {
        let url = $('#url').val();
        $(document).ready(function () {
            showHideQuestionType($("#question-type").val())
        });

        $(document).on("change", "#question-type", function (event) {  
            showHideQuestionType($("#question-type").val())
        });

        $(document).on("click", "#create-option", function (event) {
            $("#question_bank div.multiple-options").html("");
            $(".editMultipleOption").addClass("d-none");

            var number_of_option = $("#number_of_option").val();
            if (number_of_option < 2) {
                toastr.warning('Please enter number of options', 'Warning', {
                    timeOut: 5000
                })
            }
            for (var i = 1; i <= number_of_option; i++) {
                var appendRow = `<div class="multiple-choice">
                                    <div class="row ">
                                        <div class="col-lg-8">
                                            <div class="mcq_question_setup rounded-0">
                                                <label class="text-white" for="">Option ${i} ${i === 1 ? '*' : ''}</label>
                                                <input class="primary-input question_text_input form-control" placeholder="Write option " type="text" name="option[${i}][title]" value="">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="mcq_switch pt-30">
                                                <label class="switch_toggle">
                                                    <input type="checkbox" value="1" name="option[${i}][answer]"  class="switch-input11">
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                    </div>
                                    </div>
                                </div>`;
                $(".multiple-options").append(appendRow);
            }
        });
        
        $(document).on("click", "#create-image-option", function (event) {
            
            $("#question_bank div.multiple-images").html("");

            var number_of_option = $("#number_of_image_option").val();
            for (var i = 1; i <= number_of_option; i++) {
                var appendRow = "";
                appendRow += "<div class='row  mt-25'>";
                appendRow += "<div class='col-lg-6'>";
                appendRow += "<div class='input-effect'>";
               
                appendRow += "<label class='primary-btn fix-gr-bg multiple_images'><i class='fa fa-image'></i> <span class='show_file_name" + i + "'>No File Chosen [650x450]</span> <input class='change-event-to-upload' type='file' data-id='"+i+"' name='image["+i+"][new]' id='question_image" + i + "' style='display: none;'></label>";
                appendRow += "</div>";
                appendRow += "</div>";
                appendRow += "<div class='col-lg-4'>";
                appendRow += "<img class='option_image' id='image_preview" + i + "' style='width:100px;height:auto;max-height: 150px;' src='" + url + "/modules/onlineexam/no_image.jpg' />";
                appendRow += "</div>";
                appendRow += "<div class='col-lg-2 mt-10'>";

                appendRow += ` <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                <input name="image[${i}][answer]" value='1' type="checkbox">
                                <span class="checkmark"></span>
                            </label>`;

                appendRow += "</div>";
                appendRow += "</div>";

                $(".multiple-images").append(appendRow);
            }
        });

        $(document).on('change','.change-event-to-upload',function(){
            let id = $(this).attr('data-id');
            uploadImage(id);
        })
      
        $(document).on("click", "#create-pair-question", function (event) {
           
            $("#question_bank div.pair_match_div").html("");
            var number_of_option = $("#number_of_image_pair_question").val();
            for (var i = 1; i <= number_of_option; i++) {
                console.log(i);
                var appendRow = "";
                appendRow += "<div class='row  mt-25'>";
                appendRow += "<div class='col-lg-6'>";
                appendRow += "<div class='input-effect'>";
                appendRow += "<label class='primary-btn fix-gr-bg multiple_images'><i class='fa fa-image'></i> <span class='show_file_name" + i + "'>No File Chosen [650x450]</span> <input type='file' class='change-event-to-upload' data-id='"+i+"'  name='image["+i+"][new]' id='question_image" + i + "' style='display: none;'></label>";
                appendRow += "</div>";
                appendRow += "</div>";

                appendRow += "<div class='col-lg-4'>";
                appendRow += "<img class='option_image' id='image_preview" + i + "' style='width:100px;height:auto;max-height: 150px;' src='" + url + "/modules/onlineexam/no_image.jpg' />";
                appendRow += "</div>";

                appendRow += "<div class='col-lg-12 input-effect'>";
                appendRow += "<input class='primary-input form-control' type='text' placeholder='Title ' style='padding-left: 17px;' name='image[" + i + "][title]' value=''>";
               
                appendRow += "</div>";
                appendRow += "</div>";
                $(".pair_match_div").append(appendRow);
            }
        });

        $(document).on("change", "#online_exam_class",function () {
            var url = $("#url").val();
            var formData = {
                id: $(this).val(),
            };
            $('#selectSectionss').html('');
            $('#selectSectionss').multiselect('reload');
            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/onlineexam/" + "ajaxSectionStudent",
                success: function (data) {
                    var a = "";
                    $.each(data, function (i, item) {
                        if (item.length) {
                            $.each(item, function (i, section) {
                                $("#selectSectionss").append(
                                    $("<option>", {
                                        value: section.id,
                                        text: section.section_name,
                                    })
                                );
                            });
                        }
                    });

                    $('#selectSectionss').multiselect('reload');
                },
                error: function (data) {
                    console.log("Error:", data);
                },
            });
        });

        $(document).on("change",".switch-negative_marking", function () {
            if ($(this).is(":checked")) {
                var status = "1";
            } else {
                var status = "0";
            }
            var deduct_marks = $('#deduct_marks').val();
            var formData = {
                status: status,
                deduct_marks: deduct_marks,
            };
         

            var url = $("#url").val();

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/negative_marking",
                success: function (data) {
                    location.reload();
                    setTimeout(function () {
                        toastr.success(
                            "Operation Success!",
                            "Success Alert", {
                                iconClass: "customer-info",
                            }, {
                                timeOut: 2000,
                            }
                        );
                    }, 500);
                   
                },
                error: function (data) {
                   
                    setTimeout(function () {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });

        $(document).on("change", ".switch-single_page",function () {
            if ($(this).is(":checked")) {
                var status = "1";
            } else {
                var status = "0";
            }
            var deduct_marks = $('#deduct_marks').val();
            var formData = {
                status: status,
                deduct_marks: deduct_marks,
            };
           

            var url = $("#url").val();

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/single_page",
                success: function (data) {
                    location.reload();
                    setTimeout(function () {
                        toastr.success(
                            "Operation Success!",
                            "Success Alert", {
                                iconClass: "customer-info",
                            }, {
                                timeOut: 2000,
                            }
                        );
                    }, 500);
                   
                },
                error: function (data) {
                   
                    setTimeout(function () {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });

        $(document).on("keyup","#deduct_marks", function () {
            if ($(this).is(":checked")) {
                var status = "1";
            } else {
                var status = "0";
            }
            var deduct_marks = $('#deduct_marks').val();
            var formData = {
                status: status,
                deduct_marks: deduct_marks,
            };
           

            var url = $("#url").val();

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/negative_marking",
                success: function (data) {
                    location.reload();
                    setTimeout(function () {
                        toastr.success(
                            "Operation Success!",
                            "Success Alert", {
                                iconClass: "customer-info",
                            }, {
                                timeOut: 2000,
                            }
                        );
                    }, 500);
                  
                },
                error: function (data) {
                  
                    setTimeout(function () {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });

        $(document).on("change",".switch-any_question_access", function () {
            if ($(this).is(":checked")) {
                var status = "1";
            } else {
                var status = "0";
            }
            var formData = {
                status: status,
            };
            var url = $("#url").val();
            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/switch-any_question_access",
                success: function (data) {
                    location.reload();
                    setTimeout(function () {
                        toastr.success(
                            "Operation Success!",
                            "Success Alert", {
                                iconClass: "customer-info",
                            }, {
                                timeOut: 2000,
                            }
                        );
                    }, 500);
                  
                },
                error: function (data) {
                   
                    setTimeout(function () {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });
        $(document).on("change",".switch-submit_from_last_page", function () {
            if ($(this).is(":checked")) {
                var status = "1";
            } else {
                var status = "0";
            }
            var formData = {
                status: status,
            };
          

            var url = $("#url").val();

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/switch-submit_from_last_page",
                success: function (data) {
                    location.reload();
                    setTimeout(function () {
                        toastr.success(
                            "Operation Success!",
                            "Success Alert", {
                                iconClass: "customer-info",
                            }, {
                                timeOut: 2000,
                            }
                        );
                    }, 500);
                  
                },
                error: function (data) {
                   
                    setTimeout(function () {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });

        $(document).on("change", ".switch-random_question", function () {
            if ($(this).is(":checked")) {
                var status = "1";
            } else {
                var status = "0";
            }
            var formData = {
                status: status,
            };
         

            var url = $("#url").val();

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/random_question",
                success: function (data) {
                    location.reload();
                    setTimeout(function () {
                        toastr.success(
                            "Operation Success!",
                            "Success Alert", {
                                iconClass: "customer-info",
                            }, {
                                timeOut: 2000,
                            }
                        );
                    }, 500);
                   
                },
                error: function (data) {
                  
                    setTimeout(function () {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });

        $(document).on("click", "#create-image-question", function (event) {
            $("#question_bank div.multiple-images-question").html("");
            var number_of_option = $("#number_of_image_question").val();
            for (var i = 1; i <= number_of_option; i++) {
                var appendRow = "";
                appendRow += "<div class='row  mt-25'>";
                appendRow += "<div class='col-lg-6 mt-10'>";
                appendRow += "<div class='input-effect'>";
              
                appendRow += "<label class='primary-btn fix-gr-bg multiple_images'><i class='fa fa-image'></i> <span class='show_file_name" + i + "'>No File Chosen [650x450]</span> <input type='file' class='change-event-to-upload' data-id='"+i+"' name='image["+i+"][new]' id='question_image" + i + "' style='display: none;'></label>";

                appendRow += "</div>";
                appendRow += "</div>";
                appendRow += "<div class='col-lg-4'>";
                appendRow += "<img class='option_image' id='image_preview" + i + "' style='width:100px;height:auto;max-height: 150px;' src='" + url + "/modules/onlineexam/no_image.jpg' />";
                appendRow += "</div>";
                appendRow += "<div class='col-lg-2 mt-10'>";

                appendRow += ` <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                        <input name="image[${i}][answer]" value='1' type="checkbox">
                        
                    </label>`;

                appendRow += "</div>";
                appendRow += "<div class='col-lg-10 input-effect mt-2'>";
                appendRow += "<input class='primary-input form-control' type='text' style='padding-left: 17px;'  placeholder='Title ' name='image["+i+"][title]' value=''>";
                 appendRow += "</div>";
                appendRow += "</div>";
                $(".multiple-images-question").append(appendRow);
            }
        });

        $(document).on("change",".switch-auto_marking_default", function () {
            if ($(this).is(":checked")) {
                var status = "1";
            } else {
                var status = "0";
            }
            var formData = {
                status: status,
            };
           

            var url = $("#url").val();

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/auto-marking-make-default",
                success: function (data) {
                    location.reload();
                    setTimeout(function () {
                        toastr.success(
                            "Operation Success!",
                            "Success Alert", {
                                iconClass: "customer-info",
                            }, {
                                timeOut: 2000,
                            }
                        );
                    }, 500);
                    
                },
                error: function (data) {
                    
                    setTimeout(function () {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        });

        $(document).on("change","#online_homework_class", function () {
                var formData = {
                    id: $(this).val(),
                };

                var url = $("#url").val();

                $.ajax({
                    type: "GET",
                    data: formData,
                    dataType: "json",
                    url: url + "/onlineexam/" + "ajaxSectionStudent",
                    success: function (data) {
                        console.log(data);
                    $("#homework_sections").empty();

                        var appendRow = "";

                        appendRow += "<div class='col-lg-12'>";
                        appendRow += "<label>Select Section *</label>";
                        appendRow += "<div class='input-effect text-white'>";
                        appendRow +=
                            "<input type='checkbox' id='select_all' class='common-checkbox subject-checkbox'  name='' value='select_all_section' >";
                        appendRow +=
                            "<label for='select_all'>Select All</label>";
                        appendRow += "</div>";
                        appendRow += "<div id='show_all_sctions'>";
                        $.each(data, function (i, item) {
                            $.each(item, function (i, value) {
                                appendRow += "<div class='input-effect text-white'>";
                                appendRow +=
                                    "<input type='checkbox' id='section_" +
                                    value.id +
                                    "' class='common-checkbox subject-checkbox single_section' name='section_ids[]' value='" +
                                    value.id +
                                    "' >";
                                appendRow +=
                                    "<label for='section_" +
                                    value.id +
                                    "'>" +
                                    value.section_name +
                                    "</label>";
                                appendRow += "</div>";
                            });
                        });

                        appendRow += "</div>";
                        appendRow += "<div class='col-lg-12'>";

                        console.log(appendRow);
                        $("#homework_sections").append(appendRow);
                    },
                    error: function (data) { },
                });
        });

        $(document).on("change","#online_exam_class", function () {
            var formData = {
                id: $(this).val(),
            };

            var url = $("#url").val();

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "onlineexam/ajaxHomeworkSubjectDropdown",
                success: function (data) {
                    var a = "";
                    $.each(data, function (i, item) {
                        if (item.length) {
                            $("#subjectSelect").find("option").not(":first").remove();
                            $("#subjectSelecttDiv ul").find("li").not(":first").remove();
                            $.each(item, function (i, subjectsName) {
                                $("#subjectSelect").append(
                                    $("<option>", {
                                        value: subjectsName.id,
                                        text: subjectsName.subject_name,
                                    })
                                );
                                $("#subjectSelecttDiv ul").append(
                                    "<li data-value='" +
                                    subjectsName.id +
                                    "' class='option'>" +
                                    subjectsName.subject_name +
                                    "</li>"
                                );
                            });
                            $('#select_subject_loader').removeClass('pre_loader');
                            $('#select_subject_loader').addClass('loader');
                        } else {
                            $("#subjectSelecttDiv .current").html("Subject *");
                            $("#subjectSelect").find("option").not(":first").remove();
                            $("#subjectSelecttDiv ul").find("li").not(":first").remove();
                        }
                    });
                },
                error: function (data) { },
            });
        });

    
        $(document).on('click', '#select_all', function () {
            var status = this.checked; 
            $('.single_section').each(function () { 
                this.checked = status; 
            });
        });

        $(document).on('click', '.single_section', function () {
          
            if (this.checked == false) {
                $("#select_all")[0].checked = false; 
            }

           
            if ($('.single_section:checked').length == $('.single_section').length) {
                $("#select_all")[0].checked = true; 
            }
        });


        let hasRecordId = $("#record_id");
        let hasOnlineExamId = $("#online_exam_id");
        if(hasRecordId.length && hasOnlineExamId.left_time){
            let record_id = $('#record_id').val();
             let online_exam_id = $('#online_exam_id').val();
            $.ajax({
                type:'GET',
                data:{record_id:record_id, online_exam_id:online_exam_id},
                dataType:'json',
                url:url+"/onlineexam/student-online-exam-ajax",
                success:function(result){
                   
                    $('#start_time').val(result.created_at);
                    $('#last_entry_time').val(result.last_entry_time);
                    timeCalculation(result.last_entry_time);
                },
                error:function(){

                }
            });
        }


        var fileInput = document.getElementById("question_upload_content_file3");
        if (fileInput) {
            fileInput.addEventListener("change", showFileName);

            function showFileName(event) {
            
                var fileInput = event.srcElement;
                var fileName = fileInput.files[0].name;
                $('.question_placeholderUploadContent3').attr("placeholder", fileName);
            }
        }
        var fileInput = document.getElementById("question_upload_content_file1");
        if (fileInput) {
            fileInput.addEventListener("change", showFileName);
            
            function showFileName(event) {
                
                var fileInput = event.srcElement;
                var fileName = fileInput.files[0].name;
                $('.question_placeholderUploadContent1').attr("placeholder", fileName);
            }
        }
        var fileInput = document.getElementById("question_upload_content_file2");
        if (fileInput) {
            fileInput.addEventListener("change", showFileName);

            function showFileName(event) {
                var fileInput = event.srcElement;
                var fileName = fileInput.files[0].name;
                $('.question_placeholderUploadContent2').attr("placeholder", fileName);
            }
        }
    });
})(jQuery);
