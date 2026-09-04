"use strict";

function updateFileName(inputId, fileInput) {
    var fileName = fileInput.files[0].name;
    document.getElementById(inputId).value = fileName;
}

$(document).ready(function() {
    $('.subcategory').on({
        mouseenter: function() {
            let id = $(this).data('sub_cat_id');
            $('#editbtn_' + id).removeClass('d-none');
            $('#deletebtn_' + id).removeClass('d-none');
        },
        mouseleave: function() {
            let id = $(this).data('sub_cat_id');
            $('#editbtn_' + id).addClass('d-none');
            $('#deletebtn_' + id).addClass('d-none');
        },

    });
    $(document).on('click', '.deleteCategory', function() {
        var url = $("#url").val();
        let id = $(this).data('id');
        let delete_url = url + "/lms/category/delete/" + id;
        $('#deleteStudentTypeModal').modal('show');
        $("#delurl").attr("href", delete_url);
    });
});