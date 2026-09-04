function readURL(input) {
    "use script"
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#blah').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]); 
    }
}

(function($){
    "use strict"

     CKEDITOR.editorConfig = function(config) {
            config.language = 'es';
            config.uiColor = '#F7B42C';
            config.height = 300;
            config.toolbarCanCollapse = true;
            config.extraPlugins = 'imageuploader';
        };
        CKEDITOR.replace('question_answer');
        CKEDITOR.replace('question', {
            filebrowserUploadUrl: "{{ route('ckeditor_upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form',
            on: {
                instanceReady: function() {
                    this.dataProcessor.htmlFilter.addRules({
                        elements: {
                            img: function(el) {
                                // Add an attribute.
                                if (!el.attributes.alt)
                                    el.attributes.alt = 'Question image';

                                // Add some class.
                                el.addClass('feature_image_ck');
                            }
                        }
                    });
                }
            }
        });

        

        $(document).ready(function(){
            $(document).on('change',"#imgInp", function() {
                readURL(this);
            });
        });
})(jQuery)