(function($){
    'use strict'

    $(document).ready(function(){
        
         $('div.dataTables_wrapper img').css({
            'width': '100px',
            'height': 'auto'
        });

        $(document).on('change', '.common-checkbox', function() {
            let url = $("#url").val();
            let onlineExamId = $("#online_exam_id_ajax").val();
            let questionBankId = $(this).val();
            if (questionBankId == 'on') {
                $('#qustion_set').removeClass('d-none');

                return;
            } else {
                $('#qustion_set').addClass('d-none');

            }
            let checkbox = '';

            if ($(this).is(':checked')) {
                checkbox = $(this).val();
            }
            var count = $("[type='checkbox']:checked").length;
            if (count == 0) {
                $('#published_now').addClass('d-none');
            }
            $.ajax({
                type: "GET",
                dataType: "Json",
                data: {
                    questions: questionBankId,
                    online_exam_id: onlineExamId,
                    checkbox: checkbox
                },
                url: url + "/" + "onlineexam/module-online-exam-question-assign",
                success: function(data) {
                    if (data == "success") {
                        if (count > 0) {
                            $('#published_now').removeClass('d-none');
                        } else {
                            $('#published_now').addClass('d-none');
                        }

                        toastr.success('Operation successful', 'Successful', {
                            timeOut: 5000
                        })

                    } else {
                        toastr.error('Operation Failed', 'Failed', {
                            timeOut: 5000
                        })
                    }
                }
            });
        });

        $(document).on('click', '.feature_image_ck', function() {
            $('.question_image_url').attr('src', this.src);
            $('.question_image_preview').modal('show');
        })

        $('div.dataTables_wrapper img').css({
            'width': '100px',
            'height': 'auto'
        });

    });

})(jQuery)