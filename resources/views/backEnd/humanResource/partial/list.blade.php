<div class="row full_wide_table {{ generalSetting()->student_grid_view == 0 ? 'student _view list' : '' }}">
    <div class="col-lg-12">
        <div class="white-box">
            <div class="row">
                <div class="col-lg-4 no-gutters">
                    <div class="main-title">
                        <h3 class="mb-15">@lang('hr.staff_list')</h3>
                    </div>
                </div>
            </div>
            <x-table>
                <table id="table_id" class="table data-table no-footer dtr-inline collapsed" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @if (moduleStatusCheck('Branch'))
                            <th>@lang('common.branch')</th>
                            @endif
                            <th>@lang('hr.staff_no')</th>
                            <th>@lang('common.name')</th>
                            <th>@lang('hr.role')</th>
                            <th>@lang('hr.department')</th>
                            <th>@lang('hr.designation')</th>
                            <th>@lang('common.mobile')</th>
                            <th>@lang('common.email')</th>
                            <th>@lang('common.status')</th>
                            <th>@lang('common.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                
                    </tbody>
                </table>
            </x-table>
        </div>
    </div>
    </div>
</div>
@include('backEnd.partials.data_table_js')
@include('backEnd.partials.server_side_datatable')
@push('script')  

<script>
   $(document).ready(function() {
       $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            "ajax": $.fn.dataTable.pipeline( {
                url: "{{route('staff_directory_ajax')}}",
                data: { 
                @if (moduleStatusCheck('Branch'))
                branch_id  : $('#branch_id').val(),
                @endif
                role_id  : $('#role_id').val(),
                staff_no : $('#staff_no').val(),
                staff_name : $('#staff_name').val()
                },
                pages: "{{generalSetting()->ss_page_load}}" // number of pages to cache
                
            } ),
            columns: [
                @if (moduleStatusCheck('Branch'))
                {data: 'branch', name: 'branch.branch_name'},
                @endif
                {data: 'staff_no', name: 'staff_no'},
                {data: 'full_name', name: 'full_name'},
                {data: 'roles.name', name: 'roles.name'},
                {data: 'departments.name', name: 'departments.name'},
                {data: 'designations.title', name: 'designations.title'},
                {data: 'mobile', name: 'mobile'},
                {data: 'email', name: 'email'},
                {data: 'switch', name: 'switch'},
                {data: 'action', name: 'action', orderable: false, searchable: true},
            ],
            bLengthChange: false,
            bDestroy: true,
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
            titleAttr: window.jsLang("export_to_pdf"),
            exportOptions: {
                columns: 'th:not(:last-child)',
                columns: ':visible:not(.not-export-col)'
                },
            orientation: "landscape",
            pageSize: "A4",
            margin: [0, 0, 0, 12],
            alignment: "center",
            header: true,
            customize: function (doc) {
                doc.content[1].margin = [100, 0, 100, 0]; //left, top, right, bottom
                doc.content.splice(1, 0, {
                margin: [0, 0, 0, 12],
                alignment: "center",
                image: "data:image/png;base64," + $("#logo_img").val(),
                });
                doc.defaultStyle = {
                font: "DejaVuSans",
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
        }, ],
        responsive: true,
    });
} );
</script>
@endpush