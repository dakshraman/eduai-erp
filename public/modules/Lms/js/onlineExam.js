"use strict";

var url = $('#url').val();
$(document).ready(function () {
    var question_type_name = $("#lms-question-type option:selected").text();
    $('#type_name_show').text(question_type_name);

    $("#multiple-choice").hide();
    $("#true-false").hide();
    $("#multiple-image-section").hide();
    $("#fill-in-the-blanks").hide();
    $("#short_answer").hide();
    $("#image-question").hide();
    $("#pair-match").hide();
    $("#pm_option_input").hide();
    $("#multiple-options").html("");

    showHideQuestionType($("#lms-question-type").val())
});

$(document).on("change", "#lms-question-type", function (event) {
    var question_type = $(this).val();
    
    showHideQuestionType(question_type)
});

function showHideQuestionType(question_type) {
    var question_type_name = $("#lms-question-type option:selected").text();
    $('#type_name_show').text(question_type_name);
    if (question_type == "") {
        $("#multiple-choice").hide();
        $("#multiple-image-section").hide();
        $("#true-false").hide();
        $("#fill-in-the-blanks").hide();
        $("#short_answer").hide();
        $("#image-question").hide();
        $("#pair-match").hide();
        $("#multiple-options").html("");
    } else if (question_type == "M") {
        $("#multiple-choice").show();
        $("#true-false").hide();
        $("#multiple-image-section").hide();
        $("#short_answer").hide();
        $("#image-question").hide();
        $("#pair-match").hide();
        $("#fill-in-the-blanks").hide();
    } else if (question_type == "T") {
        $("#multiple-choice").hide();
        $("#true-false").show();
        $("#multiple-image-section").hide();
        $("#fill-in-the-blanks").hide();
        $("#short_answer").hide();
        $("#pair-match").hide();
        $("#multiple-options").html("");
    } else if (question_type == "MI") {

        $("#multiple-choice").hide();
        $("#true-false").hide();
        $("#multiple-image-section").show();
        $("#fill-in-the-blanks").hide();
        $("#short_answer").hide();
        $("#image-question").hide();
        $(".pair-match").hide();
        $("#multiple-options").html("");
    } else if (question_type == "SA") {
        $("#multiple-choice").hide();
        $("#true-false").hide();
        $("#multiple-image-section").hide();
        $("#fill-in-the-blanks").hide();
        $("#short_answer").show();
        $("#image-question").hide();
        $(".pair-match").hide();
        $("#multiple-options").html("");
    } else if (question_type == "IMQ") {
        $("#multiple-choice").hide();
        $("#true-false").hide();
        $("#multiple-image-section").hide();
        $("#fill-in-the-blanks").hide();
        $("#short_answer").hide();
        $(".pair-match").hide();
        $("#image-question").show();
        $("#multiple-options").html("");
    } else if (question_type == "PM") {
        $("#multiple-choice").hide();
        $("#true-false").hide();
        $("#multiple-image-section").hide();
        $("#fill-in-the-blanks").hide();
        $("#short_answer").hide();
        $(".pair-match").show();
        $("#pm_option_input").show();
        $("#image-question").hide();
        $("#multiple-options").html("");
    } else if (question_type == "SUBQ") {
        $("#multiple-choice").hide();
        $("#true-false").hide();
        $("#multiple-image-section").hide();
        $("#fill-in-the-blanks").hide();
        $("#short_answer").hide();
        $(".pair-match").hide();
        $("#image-question").hide();
        $("#multiple-options").html("");
    } else {
        $("#multiple-choice").hide();
        $("#true-false").hide();
        $("#multiple-image-section").hide();
        $("#short_answer").hide();
        $(".pair-match").hide();
        $("#fill-in-the-blanks").show();
        $("#image-question").hide();
        $("#multiple-options").html("");
    }
}

$(document).on("click", "#create-option", function (event) {
    $("#multiple-options").html("");

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
                    <label class="text-white" for="">Option ${i}</label>
                <input class="primary-input question_text_input form-control" placeholder="Write option ${i}*" type="text" name="option[]" value="">
                </div>
            </div>
            <div class="col-4">
                <div class="mcq_switch pt-30">
                      <label class="switch_toggle">
                        <input type="checkbox" value="1" name="option_check_${i}"  class="switch-input11">
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
    $("#multiple-images").html("");

    var number_of_option = $("#number_of_image_option").val();
    for (var i = 1; i <= number_of_option; i++) {
        var appendRow = "";
        appendRow += "<div class='row  mt-25'>";
        appendRow += "<div class='col-lg-6'>";
        appendRow += "<div class='input-effect'>";
        appendRow += "<label class='primary-btn fix-gr-bg multiple_images'><i class='fa fa-image'></i> <span class='show_file_name" + i + "'>No File Chosen [650x450]</span> <input type='file' onChange='uploadImage(" + i + ")' name='images[]' id='question_image" + i + "' style='display: none;'></label>";
        appendRow += "</div>";
        appendRow += "</div>";
        appendRow += "<div class='col-lg-4'>";
        appendRow += "<img class='option_image' id='image_preview" + i + "' style='width:100px;height:auto;max-height: 150px;' src='" + url + "/modules/onlineexam/no_image.jpg' />";
        appendRow += "</div>";
        appendRow += "<div class='col-lg-2 mt-10'>";

        appendRow += ` <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                        <input name="option_check_${i}" value='1' type="checkbox">
                        <span class="checkmark"></span>
                    </label>`;

        appendRow += "</div>";
        appendRow += "</div>";

        $(".multiple-images").append(appendRow);
    }
});
//pair match
$(document).on("click", "#create-pair-question", function (event) {
    $("#pair_match_div").html("");

    var number_of_option = $("#number_of_image_pair_question").val();
    for (var i = 1; i <= number_of_option; i++) {
        var appendRow = "";
        appendRow += "<div class='row  mt-25'>";
        appendRow += "<div class='col-lg-6'>";
        appendRow += "<div class='input-effect'>";
        appendRow += "<label class='primary-btn fix-gr-bg multiple_images'><i class='fa fa-image'></i> <span class='show_file_name" + i + "'>No File Chosen [650x450]</span> <input type='file' onChange='uploadImage(" + i + ")' name='images[]' id='question_image" + i + "' style='display: none;'></label>";
        appendRow += "</div>";
        appendRow += "</div>";

        appendRow += "<div class='col-lg-4'>";
        appendRow += "<img class='option_image' id='image_preview" + i + "' style='width:100px;height:auto;max-height: 150px;' src='" + url + "/modules/onlineexam/no_image.jpg' />";
        appendRow += "</div>";

        appendRow += "<div class='col-lg-12 input-effect'>";
        appendRow += "<input class='primary-input form-control' type='text' placeholder='Answer *' style='padding-left: 17px;' name='image_title" + i + "' value=''>";
        appendRow += "</div>";
        appendRow += "</div>";
        $(".pair_match_div").append(appendRow);
    }
});


//Image question
$(document).on("click", "#create-image-question", function (event) {
    $("#multiple-images-question").html("");

    var number_of_option = $("#number_of_image_question").val();
    for (var i = 1; i <= number_of_option; i++) {
        var appendRow = "";
        appendRow += "<div class='row  mt-25'>";
        appendRow += "<div class='col-lg-6 mt-10'>";
        appendRow += "<div class='input-effect'>";
        appendRow += "<label class='primary-btn fix-gr-bg multiple_images'><i class='fa fa-image'></i> <span class='show_file_name" + i + "'>No File Chosen [650x450]</span> <input type='file' onChange='uploadImage(" + i + ")' name='images[]' id='question_image" + i + "' style='display: none;'></label>";
        appendRow += "</div>";
        appendRow += "</div>";
        appendRow += "<div class='col-lg-4'>";
        appendRow += "<img class='option_image' id='image_preview" + i + "' style='width:100px;height:auto;max-height: 150px;' src='" + url + "/modules/onlineexam/no_image.jpg' />";
        appendRow += "</div>";
        appendRow += "<div class='col-lg-2 mt-10'>";

        appendRow += ` <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                <input name="option_check_${i}" value='1' type="checkbox">
                <span class="checkmark"></span>
            </label>`;

        appendRow += "</div>";
        appendRow += "<div class='col-lg-10 input-effect mt-2'>";
        appendRow += "<input class='primary-input form-control' type='text' style='padding-left: 17px;'  placeholder='Answer *' name='image_title" + i + "' value=''>";
        appendRow += "</div>";
        appendRow += "</div>";
        $(".multiple-images-question").append(appendRow);
    }
});






