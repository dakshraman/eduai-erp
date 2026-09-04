

function onlineExamDelete(id) {
    "use strict"
    var modal = $('#deleteOnlineExamFromList');
    modal.find('input[name=online_exam_id]').val(id)
    modal.modal('show');
}

function duplicateExam(id) {
    "use strict"

    var modal = $('#duplicateExam');
    modal.find('input[name=online_exam_id]').val(id)
    modal.modal('show');
}

(function($){
    "use strict"

     table.columns(3).order('desc').draw();

    $(document).ready(function(){

        $(document).on('click','.duplicate',function(){
            let id = $(this).attr('data-id');
            duplicateExam(id);
        });

        $(document).on('click','.delete',function(){
            let id = $(this).attr('data-id');
            onlineExamDelete(id);
        });

        $(document).on("change","#online_exam_class2", function() {
            var formData = {
                id: $(this).val(),
            };
            var url = $("#url").val();
            $('#selectSectionss').html('');
            $('#selectSectionss').multiselect('reset');
            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/onlineexam/" + "ajaxSectionStudent",
                success: function(data) {
                    var a = "";
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $.each(item, function(i, section) {
                                $("#selectSectionss").append(
                                    $("<option>", {
                                        value: section.id,
                                        text: section.section_name,
                                    })
                                );
                            });
                            $('#selectSectionss').multiselect('reset');
                        }
                    });
                },
                error: function(data) {
                    console.log("Error:", data);
                },
            });
        });

    });

})(jQuery)