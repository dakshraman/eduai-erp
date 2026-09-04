
(function($){
    "use strict"

    $(document).ready(function () {
            $('.sa_answer').summernote({
                placeholder: 'Short Answer',
                tabsize: 2,
                height: 100,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen' ]], 
                    ['help', ['help']]
                ],
                callbacks: {
                    onImageUpload: function (files) {
                        sendFile(files, '.sa_answer')
                    }
                }
            });
            $('.subjective_answer').summernote({
                placeholder: 'Subjective Answer',
                tabsize: 2,
                height: 100,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                  
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen' ]], 
                    ['help', ['help']]
                ],
                callbacks: {
                    onImageUpload: function (files) {
                        sendFile(files, '.subjective_answer')
                    }
                }
            });

            let client_text = '';
            $('.text_answer').summernote({
                callbacks: {
                    onImageUpload: function (files) {
                        sendFile(files, '.text_answer')
                    },
                    onKeyup: function (e) {
                        setTimeout(function () {
                            let server_text = $('#server_value').val();
                            let client_text = $('.text_answer').val();
                            if (server_text != client_text) {
                                console.log('new text: ' + server_text + ' != ' + client_text);
                                $('#not_save_warning').show();
                            } else {
                                console.log('old text: ' + server_text + ' == ' + client_text);
                                $('#not_save_warning').hide();
                            }
                           
                        }, 200);
                    }
                }
            });

            
    });

})(jQuery)