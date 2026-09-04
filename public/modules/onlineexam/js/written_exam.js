function examDelete(id) {
    "use strict"

    var modal = $('#deleteOnlineExam');
    modal.find('input[name=online_exam_id]').val(id)
    modal.modal('show');
}

(function($){
    "use strict"
    $(document).ready(function(){
        CKEDITOR.editorConfig = function(config) {
            config.language = 'es';
            config.uiColor = '#F7B42C';
            config.height = 300;
            config.toolbarCanCollapse = true;
            config.extraPlugins = 'imageuploader';
        };

        CKEDITOR.replace('question_answer', {
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

        $(document).on('click','.delete',function(){
            let id = $(this).attr('data-id');
            examDelete(id);
        });

        $(document).on('change','#question_type',function() {
            if (this.value == 0) {
                $('#written_upload').show();
                $('#write_question').hide();
            } else {
                $('#written_upload').hide();
                $('#write_question').show();

            }
        });
        


    });
})(jQuery)