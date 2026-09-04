(function($){
    "use strict"
    $(document).ready(function(){
        $(document).on("change","#onlineExamSubject", function() {
            let url = $("#url").val();
            let i = 0;
            let subjectId = $(this).val();
            $.ajax({
                method: "get",
                dataType: "json",
                url: url + "/onlineexam/" + "subject-wise-group",
                data: {
                    subjectId: subjectId,
                },

                beforeSend: function() {
                    $('#selectOnlineExamGroupLoader').addClass('pre_loader');
                    $('#selectOnlineExamGroupLoader').removeClass('loader');
                },

                success: function(data) {
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $("#OnlineExamGroup").find("option").not(":first").remove();
                            $("#OnlineExamGroupDiv ul").find("li").not(":first").remove();

                            $.each(item, function(i, allGroup) {
                                $("#OnlineExamGroup").append(
                                    $("<option>", {
                                        value: allGroup.id,
                                        text: allGroup.title,
                                    })
                                );
                            });
                            $("#OnlineExamGroup").niceSelect('update');
                        } else {
                            $("#OnlineExamGroup").find("option").not(":first").remove();
                            $("#OnlineExamGroup ul").find("li").not(":first").remove();
                        }
                    });
                },
                error: function(data) {},
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $('#selectOnlineExamGroupLoader').removeClass('pre_loader');
                        $('#selectOnlineExamGroupLoader').addClass('loader');
                    }
                }
            });
        })

    });
})(jQuery)