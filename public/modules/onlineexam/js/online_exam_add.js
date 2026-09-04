 function examDelete(id) {
    "use strict"

    var modal = $('#deleteOnlineExam');
    modal.find('input[name=online_exam_id]').val(id)
    modal.modal('show');
}

function hideOrShowDurationTypeDiv(duration_type) {
    "use strict"
            if (duration_type == 'exam') {
                $('#duration_type_exam_div').show();
                $('#duration_type_question_div').hide();
                $('#duration').attr('required', true);
                $('#default_question_time').attr('required', false);
            } else if (duration_type == 'question') {
                $('#duration_type_exam_div').hide();
                $('#duration_type_question_div').show();

                $('#duration').attr('required', false);
                $('#default_question_time').attr('required', true);
            } else {
                $('#duration_type_exam_div').show();
                $('#duration_type_question_div').hide();
                $('#duration').attr('required', true);
                $('#default_question_time').attr('required', false);
            }
        }

(function($){
    "use strict"
    $(document).ready(function(){
        let sectionRequest = null;

        $(document).ready(function() {
            hideOrShowDurationTypeDiv($('.duration_type').val());
        });

        $(document).on('change', '.duration_type', function() {
            hideOrShowDurationTypeDiv($(this).val());
        });


         $(".onlineExam_subject").on("change", function() {
            $("#checkbox_section_groups").prop("checked", false);
            var url = $("#url").val();
            let subject_id = $(this).val();

            var formData = {
                subject_id: subject_id,
            };

            $("#selectQuestionGroups").empty();
            // get question group
            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/onlineexam/" + "get-question-group",
                beforeSend: function() {
                    $('#select_group_loader').addClass('pre_loader');
                   
                },
                success: function(data) {
                    console.log(data.groups);
                    let html = '';
                    for(let i = 0; i < data.groups.length; i++){
                         html = html+`<option value='${data.groups[i].id}'>${data.groups[i].title}</option>`;                        
                    }                    
                    $('#selectQuestionGroups').html(html);
                    $("#selectQuestionGroups").multiselect('reload');
                   
                },
                complete:function(){
                      $('#select_group_loader').removeClass('pre_loader');
                },
                error: function(data) {
                    console.log("Error:", data);
                },
            });
        });
        $(document).off('change', '.onlineExamClass').on('change.onlineExamSections', '.onlineExamClass', function() {
            var url = $("#url").val();
            var formData = {
                id: $(this).val(),
            };
            var $sections = $('#selectSectionss');

            if (sectionRequest) {
                sectionRequest.abort();
            }

            $sections.empty().multiselect('reload');
            sectionRequest = $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/onlineexam/" + "ajaxSectionStudent",
                success: function (data) {
                    var sectionNames = {};
                    $sections.empty();
                    $.each(data, function (i, item) {
                        if (item.length) {
                            $.each(item, function (i, section) {
                                var sectionName = $.trim(section.section_name || '');
                                var sectionKey = sectionName.toLowerCase();

                                if (!sectionName || sectionNames[sectionKey]) {
                                    return;
                                }

                                sectionNames[sectionKey] = true;
                                $sections.append(
                                    $("<option>", {
                                        value: section.id,
                                        text: sectionName,
                                    })
                                );
                            });
                        }
                    });

                    $sections.multiselect('reload');
                },
                error: function (data) {
                    console.log("Error:", data);
                },
                complete: function () {
                    sectionRequest = null;
                },
            });
        });

        $(document).on("change",".sectionOnlineExam", function() {

            var url = $("#url").val();
            let class_id = $(".onlineExamClass").val();
            let sections = $(this).val();
            var i = 0;
            var formData = {
                sections: sections,
                class_id: class_id,
            };


            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/onlineexam/" + "get-subject",

                beforeSend: function() {
                    $('#select_subject_loader').addClass('pre_loader');
                    $('#select_subject_loader').removeClass('loader');
                },
                success: function(data) {
                    console.log(data);
                    var a = "";
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $("#subjectSelect").find("option").not(":first").remove();
                            $("#subjectSelecttDiv ul").find("li").not(":first").remove();
                            $.each(item, function(i, subjectsName) {
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
                    console.log(a);
                },
                error: function(data) {},
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $('#select_subject_loader').removeClass('pre_loader');
                        $('#select_subject_loader').addClass('loader');
                    }
                }
            });
        });

        $(document).on('change','.edit-online-exam-class',function(){
            let class_id = $(this).val();
            let url = $(this).attr('data-url');
            $.ajax({
                url:url,
                method:"get",
                data:{
                    class_id: class_id
                },
                success:function(response){
                    if(response.status == 'success'){
                        $("#examSection").html(response.view);
                        $("#examSection").niceSelect('update');
                    }else{
                        toastr.error(response.msg);
                    }
                }
            })
        });

        $(document).on('change','#examSection',function(){
            let section_id = $(this).val();
            let class_id = $("#select_class").val();
            let url = $(this).attr('data-url');
            $.ajax({
                url:url,
                method: "get",
                data : {
                    class_id : class_id,
                    section_id : section_id
                },
                beforeSend: function() {
                    $('#select_section_loader').addClass('pre_loader');
                    $('#select_subject_loader').removeClass('loader');
                },
                success:function(response){
                    if(response.status == 'success'){
                        $("#select_subject").html(response.view);
                        $("#select_subject").niceSelect('update');
                    }else{
                         toastr.error(response.msg);
                    }
                },
                complete: function() {
                    $('#select_subject_loader').removeClass('pre_loader');
                    $('#select_subject_loader').addClass('loader');
                }
            });
        });

    });
})(jQuery)
