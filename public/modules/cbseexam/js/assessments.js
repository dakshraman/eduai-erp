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
                    data: 'assignment',
                    name: 'assignment',
                    orderable: true
                },
                {
                    data: 'description',
                    name: 'description',
                    orderable: false
                },
                {
                    data: 'attribute',
                    name: 'attribute',
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
            responsive: false,
        });


        $(document).on('click', '.edit', function (event) {
            event.preventDefault();
            let href = $(this).attr('href');

            $.ajax({
                url: href,
                method: "get"
            }).done(function (response) {
                if (response.status == 1) {
                    $("#append").append(response.view);
                    $("#edit_assignment_model").modal('show');
                } else {
                    toastr.error(response.msg);
                }
            });

        });


        $(document).on('click', '.add_more', function () {

            let element = $(this).attr('data-assignment');
            let id = Date.now();
            let temp = `     <div class="row" id="` + id + `">
                                    <div class="col-xl-2 col-md-6 col-lg-3 col-sm-6">
                                        <div class="primary_input mb-35">
                                            <label class="primary_input_label" for=""> Assessment Type 
                                                <span  class="text-danger">*</span> </label>
                                             <input type="text" class="primary_input_field" name="assignment_type[]" required>
                                        </div> 
                                    </div>
                                    <div class="col-xl-2 col-md-6 col-lg-3 col-sm-6">
                                        <div class="primary_input mb-35">
                                            <label class="primary_input_label" for=""> Code 
                                                <span  class="text-danger">*</span> </label>
                                             <input type="text" class="primary_input_field" name="code[]" required>
                                        </div> 
                                    </div>
                                    <div class="col-xl-2 col-md-6 col-lg-3 col-sm-6">
                                        <div class="primary_input mb-35">
                                            <label class="primary_input_label" for=""> Maximum Marks
                                                <span  class="text-danger">*</span> </label>
                                             <input type="text" class="primary_input_field" name="maximum_marks[]" required>
                                        </div> 
                                    </div>
                                    <div class="col-xl-2 col-md-6 col-lg-3 col-sm-6">
                                        <div class="primary_input mb-35">
                                            <label class="primary_input_label" for=""> Passing Percent
                                                <span  class="text-danger">*</span> </label>
                                             <input type="text" class="primary_input_field" name="pass_percentage[]" required>
                                        </div> 
                                    </div>
                                    <div class="col">
                                        <div class="primary_input mb-35">
                                            <label class="primary_input_label" for="">Description </label>
                                             <textarea name="assignment_description[]"   class="primary_input_field" rows="10"></textarea>
                                        </div>
                                    </div>
                                    <div class="col flex-shrink-0 flex-grow-0">
                                        <button class="circle-btn remove-element "  data-remove='#` + id + `'  type="button">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>`;
            $(element).append(temp);
        });
        $(document).on('click', '.remove-element', function () {
            let element = $(this).attr('data-remove');
            $(element).remove();
        });

        $(document).on('click', '.delete-item', function () {
            let id = $(this).attr('data-id');
            deleteId(id);
        });
    });
}(jQuery));