(function () {
    'use strict';
    $(document).ready(function () {
        $(document).on('change', '.class_id', function () {

            let class_id = $(this).val();
            let url = $(this).attr('data-url');
            let element = $(this).attr('data-element');

            $.ajax({
                url: url,
                method: "get",
                data: {
                    class_id: class_id,
                }
            }).done(function (response) {
                if (response.status == 'success') {
                     $(element).multiselect({
                        columns: 1,
                        placeholder: "",
                        search: true,
                        searchOptions: {
                           default: "",
                        
                        },
                        
                        selectAll: false,
                    });
                    $(element).html("");
                    $.each(response.data, function( index, item ) {                        
                       if ($(element + " option[value='" + item.id + "']").length === 0) {
                            $(element).append(new Option(item.name, item.id));
                        }
                     
                    });                   
                    $(element).multiselect('reset');
                }
            });

        });


        $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: $.fn.dataTable.pipeline({
                url: $(".data-table").attr('data-url'),
                data: {},
                pages: $('.data-table').attr('data-pages') // number of pages to cache
            }),
            columns: [{
                data: 'DT_RowIndex',
                name: 'id',
                orderable: true
            },
                {
                    data: 'name',
                    name: 'name',
                    orderable: true
                },
                {
                    data: 'class',
                    name: 'class',
                    orderable: false
                },
                {
                    data: 'term',
                    name: 'term',
                    orderable: true
                },
                {
                    data: 'subject_included',
                    name: 'subject_included',
                    orderable: false
                },
                {
                    data: 'exam_published',
                    name: 'exam_published',
                    orderable: false
                },
                {
                    data: 'result_status',
                    name: 'result_publish',
                    orderable: false
                },
                {
                    data: 'description',
                    name: 'description',
                    orderable: true
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: true
                },
            ],
            bLengthChange: false,
            bDestroy: true,
            order: [
                [1, 'asc']
            ],
            language: {
                search: "<i class='ti-search'></i>",
                searchPlaceholder: window.jsLang('quick_search'),
                paginate: {
                    next: "<i class='ti-arrow-right'></i>",
                    previous: "<i class='ti-arrow-left'></i>",
                },
            },
            dom: "Bfrtip",
            buttons: [{
                extend: "copyHtml5",
                text: '<i class="fa fa-files-o"></i>',
                title: $("#logo_title").val(),
                titleAttr: window.jsLang('copy_table'),
                exportOptions: {
                    columns: ':visible:not(.not-export-col)'
                },
            },
                {
                    extend: "excelHtml5",
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: window.jsLang('export_to_excel'),
                    title: $("#logo_title").val(),
                    margin: [10, 10, 10, 0],
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    },
                },
                {
                    extend: "csvHtml5",
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: window.jsLang('export_to_csv'),
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    },
                },
                {
                    extend: "pdfHtml5",
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: window.jsLang('export_to_pdf'),
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    },
                    orientation: "landscape",
                    pageSize: "A4",
                    margin: [0, 0, 0, 12],
                    alignment: "center",
                    header: true,
                    customize: function (doc) {
                        doc.content[1].margin = [100, 0, 100,
                            0]; // left, top, right, bottom
                        doc.content.splice(1, 0, {
                            margin: [0, 0, 0, 12],
                            alignment: "center",
                            image: "data:image/png;base64," + $("#logo_img")
                                .val(),
                        });
                        doc.defaultStyle = {
                            font: 'DejaVuSans'
                        };
                    },
                },
                {
                    extend: "print",
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: window.jsLang('print'),
                    title: $("#logo_title").val(),
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    },
                },
                {
                    extend: "colvis",
                    text: '<i class="fa fa-columns"></i>',
                    postfixButtons: ["colvisRestore"],
                },
            ],
            columnDefs: [{
                visible: false,
            },],
            responsive: true,
        });


        $(document).on('click', '.get-student', function () {
            let url = $(this).attr('data-url');
            $.ajax({
                url: url,
                method: "get"
            }).done(function (response) {
                if (response.status == 'success') {
                    $("#append").html(response.view);
                    $("#cbse_exam_student_list").modal('show');
                    let total = $('.item-checkbox').length;
                    let checked = $('.item-checkbox:checked').length;
                    $('.select-all').prop('checked', total === checked);
                } else {
                    toastr.error(response.msg);
                }
            })
        });

        $(document).on('change', '.select-all', function () {
            $('.item-checkbox').prop('checked', $(this).prop('checked'));
        });


        $(document).on('click', '.get-assign-student', function () {
            let subjectModal = $("#cbse_exam_subject_marks");
            if(subjectModal.length){
                subjectModal.modal('hide');
            }
            let url = $(this).attr('data-url');
            $.ajax({
                url: url,
                method: "get"
            }).done(function (response) {
                if (response.status == 'success') {
                    
                    $("#append").html(response.view);
                    $("#cbse_exam_student_list").modal('show');

                } else {
                    toastr.error(response.msg);
                }
            })
        });

        $(document).on('click', '.get-assign-subject', function () {
            let url = $(this).attr('data-url');
            $.ajax({
                url: url,
                method: "get"
            }).done(function (response) {
                if (response.status == 'success') {
                    $("#append").html(response.view);
                    $("#cbse_exam_subjects").modal('show');

                } else {
                    toastr.error(response.msg);
                }
            })
        });

        $(document).on('click', '.exam-marks', function () {
            let url = $(this).attr('data-url');
            $.ajax({
                url: url,
                method: "get"
            }).done(function (response) {
                if (response.status == 'success') {
                    $("#append").html(response.view);
                    $("#cbse_exam_subject_marks").modal('show');
                } else {
                    toastr.error(response.msg);
                }
            })
        });

        $(document).on('change', '.is_absense', function () {
            let field_id = $(this).attr('data-class');
            if ($(this).prop('checked')) {
                $(field_id).val(0);
                $(field_id).attr('readonly', true);
            } else {
                $(field_id).attr('readonly', false);
            }
        });

        $(document).on('click', '.edit', function (event) {
            event.preventDefault();
            let href = $(this).attr('data-url');

            $.ajax({
                url: href,
                method: "get"
            }).done(function (response) {
                console.log(response);
                if (response.status == 'success') {
                    $("#append").html(response.view);
                    
                    $("#edit_exam_model").modal('show');
                    $(".class_id").niceSelect();
                    $("#edit_assesment_id").niceSelect();
                    $("#edit_grade_id").niceSelect();
                    $("#edit_section").multiselect({
                        columns: 1,
                        placeholder: "",
                        search: true,
                        searchOptions: {
                           default: "",
                        
                        },
                        
                        selectAll: false,
                    });
                    
                } else {
                    toastr.error(response.msg);
                }
            });
        });

        $(document).on('click', '.generate-rank', function () {
            let url = $(this).attr('data-url');
            window.location.href = url;
        });

        $(document).on('click', '.item-delete', function () {
            let id = $(this).attr('data-id');
            deleteId(id);
        });

         $("select[multiple].multypol_check_select").multiselect({
            columns: 1,
            placeholder: "",
            search: true,
            selectAll: false,
        });

        $(document).on('input', '.full-attendance', function () {
            let attendance = parseInt($(this).val(), 10) || 0;
            $(".student-attendace").attr('max', attendance);
        });

    });
}(jQuery));
