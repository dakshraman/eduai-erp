(function ($) {
            'use strict';
            $(document).ready(function () {

                $(document).on('change', '#class_id', function () {
                    let url = $(this).attr('data-url');
                    let class_id = $(this).val();
                    $.ajax({
                        url: url,
                        data: {
                            class_id: class_id
                        },
                        method: "get",

                    }).done(function (response) {
                        if (response.status == 'success') {
                            $("#section_id").html(response.view);
                            $("#section_id").niceSelect('update');
                        } else {
                            toastr.error(response.message, 'error)');
                        }
                    });
                });


                $(document).on('change', '#section_id', function () {
                    let url = $(this).attr('data-url');
                    let class_id = $("#class_id").val();
                    let section_id = $("#section_id").val();
                    $.ajax({
                        url: url,
                        data: {
                            class_id: class_id,
                            section_id: section_id
                        },
                        method: "get",

                    }).done(function (response) {
                        if (response.status == 'success') {
                            $("#template_id").html(response.view);
                            $("#template_id").niceSelect('update');
                        } else {
                            toastr.error(response.message, 'error');
                        }
                    });
                });

                $(document).on('change', '.select-all', function () {
                    $('.item-checkbox').prop('checked', $(this).prop('checked'));
                });

            })
        }(jQuery))