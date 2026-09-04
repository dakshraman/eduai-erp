(function ($) {
    'use strict';
    $(document).ready(function () {
        $(document).on('change', '.fileholder', function () {
            let print_element = $(this).attr('data-print');
            let file_name = $(this).val().replace(/.*(\/|\\)/, '');
            $(print_element).attr('placeholder', file_name);
        });
        let multiSelect =  $('select[multiple].multypol_check_select');
        if(multiSelect.length){
            $("select[multiple].multypol_check_select").multiselect({
                columns: 1,
                placeholder: "",
                search: true,
                selectAll: false,
            });
        }
        

        $(document).on('change', '#class_id', function () {
            let url = $(this).attr('data-url');
            $.ajax({
                url: url,
                method: "get",
                data: {
                    'class_id': $(this).val()
                }
            }).done(function (response) {
                if (response.status == 1) {
                    $("#section_id").html(response.view);
                    $("#section_id").multiselect('reset');

                }
            })
        });

        let dataTable = $(".data-table");
        if (dataTable.length) {
            $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": $.fn.dataTable.pipeline({
                    url: $(".data-table").attr('data-url'),
                    data: {},
                    pages: $(".data-table").attr('data-pages'),

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
                        data: 'section',
                        name: 'section',
                        orderable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false
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
                                0]; //left, top, right, bottom
                            doc.content.splice(1, 0, {
                                margin: [0, 0, 0, 12],
                                alignment: "center",
                                image: "data:image/png;base64," + $("#logo_img")
                                    .val(),
                            });
                            doc.defaultStyle = {
                                font: 'DejaVuSans'
                            }
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
        }


        $(document).on('click', '.edit', function (event) {
            event.preventDefault();
            let href = $(this).attr('href');

            $.ajax({
                url: href,
                method: "get"
            }).done(function (response) {
                if (response.status == 1) {
                    $("#append").html(response.view);
                    $("#edit_term_form").modal('show');
                } else {
                    toastr.error(response.msg);
                }
            });

        });

        $(document).on('change', '#marksheet_type', function () {
            let type = $(this).val();
            let template = $("#template_id").val();
            let url = $(this).attr('data-url');
            $.ajax({
                url: url,
                data: {
                    type: type,
                    template: template
                },
                method: "get"
            }).done(function (response) {
                if (response.status == 'success') {
                    $("#exams-terms").html(response.html);
                    $("#submit-btn").removeClass('d-none');
                } else {
                    $("#submit-btn").addClass('d-none');
                }
            });
        });

        let marksheet_type = $("#marksheet_type");
        if(marksheet_type.length){
            $(function () {
                let type = $(marksheet_type).val();
                let template = $("#template_id").val();
                let url = $(marksheet_type).attr('data-url');
                $.ajax({
                    url: url,
                    data: {
                        type: type,
                        template: template
                    },
                    method: "get"
                }).done(function (response) {
                    if (response.status == 'success') {
                        $("#exams-terms").html(response.html);
                        $("#submit-btn").removeClass('d-none');
                    } else {
                        $("#submit-btn").addClass('d-none');
                    }
                });
            });            
        }

        $(document).on('change', '.single_term_wise', function () {
            var targetClass = $(this).data('class');
            $('.' + targetClass).prop('checked', $(this).is(':checked'));

        });

        $(document).on('change', '.multple-term', function () {
            var targetClass = $(this).data('class');
            $('.' + targetClass).prop('checked', $(this).is(':checked'));
        });

        $(document).on('click', '.item-delete', function () {
            let id = $(this).attr('data-id');
            deleteId(id);
        });

        
    })
}(jQuery))