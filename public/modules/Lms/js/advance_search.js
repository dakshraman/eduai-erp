"use strict";

$(document).ready(function() {
    $("#Acategory_id").on("change", function() {
        var url = $("#url").val();
        var formData = {
            id: $(this).val(),
        };
        
        if( !$(this).val()){
            $("#subcategory_id").append(
                $("<option>", {
                    value: '',
                    text: 'Select Subcategory',
                })
            );
            $('#subcategory_id').niceSelect('update')
            return ;
        }

        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/" + "admin/course/ajaxGetCourseSubCategory",
            success: function(data) {
                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#Asubcategory_id").find("option").not(":first").remove();
                        $("#AsubCategoryDiv ul").find("li").not(":first").remove();

                        $.each(item, function(i, section) {
                            $("#Asubcategory_id").append(
                                $("<option>", {
                                    value: section.id,
                                    text: section.name,
                                })
                            );

                            $("#AsubCategoryDiv ul").append(
                                "<li data-value='" +
                                section.id +
                                "' class='option'>" +
                                section.name +
                                "</li>"
                            );
                        });
                    } else {
                        $("#AsubCategoryDiv .current").html("Subcategory");
                        $("#Asubcategory_id").find("option").not(":first").remove();
                        $("#AsubCategoryDiv ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
        });
    });

    $("#Asubcategory_id").on("change", function() {
        var url = $("#url").val();
        var formData = {
            category_id     : $('#Acategory_id').val(),
            subcategory_id  : $('#Asubcategory_id').val(),
        };
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/" + "ajaxGetCourseList",
            success: function(data) {
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#Acourse_id").find("option").not(":first").remove();
                        $("#ACourseDiv ul").find("li").not(":first").remove();

                        $.each(item, function(i, course) {
                            $("#Acourse_id").append(
                                $("<option>", {
                                    value: course.id,
                                    text: course.title,
                                })
                            );
                            $("#ACourseDiv ul").append( "<li data-value='" + course.id + "' class='option'>" + course.title + "</li>");
                        });
                    } else {
                        $("#ACourseDiv .current").html("Select A Course *");
                        $("#Acourse_id").find("option").not(":first").remove();
                        $("#ACourseDiv ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
        });
    });
});


$(document).ready(function() {
    $(".edit_category_id").on("change", function() {
        var url = $("#url").val();

        var course_id = $(this).closest('#course').data('course_id');
        
        var formData = {
            id: $(this).val(),
        };
        if( !$(this).val()){
            $("#subcategory_id").append(
                    $("<option>", {
                        value: '',
                        text: 'Select Subcategory',
                    })
                );
                $('#subcategory_id').niceSelect('update')
                return ;
            }

        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/" + "admin/course/ajaxGetCourseSubCategory",
            success: function(data) {
                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#edit_subcategory_id"+course_id).find("option").not(":first").remove();
                        $("#edit_subCategoryDiv"+course_id+" ul").find("li").not(":first").remove();

                        $.each(item, function(i, section) {
                            $("#edit_subcategory_id"+course_id).append(
                                $("<option>", {
                                    value: section.id,
                                    text: section.name,
                                })
                            );

                            $("#edit_subCategoryDiv"+course_id+" ul").append(
                                "<li data-value='" +
                                section.id +
                                "' class='option'>" +
                                section.name +
                                "</li>"
                            );
                        });
                    } else {
                        $("#edit_subCategoryDiv"+course_id+".current").html("SECTION *");
                        $("#edit_subcategory_id"+course_id).find("option").not(":first").remove();
                        $("#edit_subCategoryDiv"+course_id+" ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
            },
        });

    });
});

$(document).ready(function(){
    $('#course_2').change(function(){
    if(this.checked)
        $('#price_div').fadeOut('slow');
    else
        $('#price_div').fadeIn('slow');
});

});

$(document).ready(function(){
    $('#course_3').change(function(){
        if(this.checked)
            $('#discount_price_div').fadeIn('slow');
        else
            $('#discount_price_div').fadeOut('slow');
    });
});

$(document).ready(function(){
    $('.edit_course_2').change(function(){
        var course_id=$(this).val();
        if(this.checked)
            $('#edit_price_div'+course_id).fadeOut('slow');
        else
            $('#edit_price_div'+course_id).fadeIn('slow');
    });
});

$(document).ready(function(){
        $('.edit_course_3').change(function(){
            var course_id=$(this).val();
        if(this.checked)
            $('#edit_discount_price_div'+course_id).fadeIn('slow');
        else
            $('#edit_discount_price_div'+course_id).fadeOut('slow');
    });
});
