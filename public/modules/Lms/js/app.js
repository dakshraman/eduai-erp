"use strict";

$(document).ready(function () {
    $("#lms_category").on("change", function () {
        var url = $("#url").val();
        $('#lms_subcategory_loader').removeClass('loader').addClass('pre_loader');
        var formData = {
            parent_id: $(this).val(),
        };
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/" + "lms/get-subcategory",
            success: function (data) {
                var a = "";
                $.each(data, function (i, item) {
                    if (item.length) {
                        $("#lms_subcategory").find("option").not(":first").remove();
                        $("#lms_subcategory_div ul").find("li").not(":first").remove();

                        $.each(item, function (i, subcategory) {
                            $("#lms_subcategory").append(
                                $("<option>", {
                                    value: subcategory.id,
                                    text: subcategory.category_name,
                                })
                            );

                            $("#lms_subcategory_div ul").append(
                                "<li data-value='" +
                                subcategory.id +
                                "' class='option'>" +
                                subcategory.category_name +
                                "</li>"
                            );
                        });
                        $('#lms_subcategory_loader').removeClass('pre_loader').addClass('loader');

                    } else {
                        $("#lms_subcategory_div .current").html("Select SubCategory");
                        $("#lms_subcategory").find("option").not(":first").remove();
                        $("#lms_subcategory_div ul").find("li").not(":first").remove();
                        $('#lms_subcategory_loader').removeClass('pre_loader').addClass('loader');

                    }
                });
            },
            error: function (data) {
                console.log("Error:", data);
                $('#lms_subcategory_loader').removeClass('pre_dloader').addClass('dloader');

            },
        });
    });

});
