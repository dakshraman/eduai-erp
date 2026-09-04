(function ($) {
    'use strict';
    $(document).ready(function () {
        $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            "ajax": $.fn.dataTable.pipeline({
                url: $(".data-table").attr('data-url'),
                data: {},
                pages: $(".data-table").attr('data-pages'), // number of pages to cache

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
                    data: 'description',
                    name: 'description',
                    orderable: false
                },
                {
                    data: 'parameters',
                    name: 'parameters',
                    orderable: true
                },
                {
                    data: 'marks',
                    name: 'marks',
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

        $(document).on('click', '.edit', function (event) {
            event.preventDefault();
            let href = $(this).attr('href');

            $.ajax({
                url: href,
                method: "get"
            }).done(function (response) {
                if (response.status == 1) {
                    $("#append").html(response.view);
                    $("#edit_observation_model").modal('show');
                    $(".nice-select").niceSelect();
                } else {
                    toastr.error(response.msg);
                }
            });
        });

        $(document).on('click', '.item-delete', function () {
            let id = $(this).attr('data-id');
            deleteId(id);
        });


    });
}(jQuery));