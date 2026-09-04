
function sendFile(files, editor = '#summernote') {
    "use strict"

    let url = $("#url").val();
    let formData = new FormData();
    $.each(files, function(i, v) {
        formData.append("files[" + i + "]", v);
    })

    $.ajax({
        url: url + '/onlineexam/upload-file',
        type: 'post',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function(response) {
            let $summernote = $(editor);
            $.each(response, function(i, v) {
                $summernote.summernote('insertImage', v);
            })
        },
        error: function(data) {
            if (data.status === 404) {
                toastr.error("What you are looking is not found", 'Opps!');
                return;
            } else if (data.status === 500) {
                toastr.error('Something went wrong. If you are seeing this message multiple times, please contact Spondon It author.', 'Opps');
                return;
            } else if (data.status === 200) {
                toastr.error('Something is not right', 'Error');
                return;
            }
            let jsonValue = $.parseJSON(data.responseText);
            let errors = jsonValue.errors;
            if (errors) {
                let i = 0;
                $.each(errors, function(key, value) {
                    let first_item = Object.keys(errors)[i];
                    let error_el_id = $('#' + first_item);
                    if (error_el_id.length > 0) {
                        error_el_id.parsley().addError('ajax', {
                            message: value,
                            updateClass: true
                        });
                    }
                    toastr.error(value, 'Validation Error');
                    i++;
                });
            } else {
                toastr.error(jsonValue.message, 'Opps!');
            }

        }
    });
}