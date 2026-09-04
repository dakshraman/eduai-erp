"use strict";

$(document).ready(function () {
    $(document).on('click', '.recurring-type', function () {
        const isRecurring = $(this).val() !== "0";
        $(".recurrence-section-hide").toggle(isRecurring);
    });

    $(".default-settings").hide();

    $(document).on('click', '.chnage-default-settings', function () {
        const isDefault = $(this).val() !== "0";
        $(".default-settings").toggle(isDefault);
    });
});

$("#search-icon").on("click", function() {
    $("#search").focus();
});

$(".primary-input.date").datepicker({
    autoclose: true,
    setDate: new Date(),
});

$(".primary-input.time").datetimepicker({
    format: "LT",
});

if ($(".niceSelect1").length) {
    $(".niceSelect1").niceSelect();
}