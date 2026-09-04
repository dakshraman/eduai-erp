{{-- List View --}}
<div class="row full_wide_table {{ generalSetting()->student_grid_view == 0 ? 'student_view list' : '' }}">
    <div class="col-lg-12 px-0">
        <div class="white-box">
            <div class="row">
                <div class="col-lg-4 no-gutters">
                    <div class="main-title">
                        <h3 class="mb-15">@lang('student.student_list')</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <x-table>
                        <table id="table_id"
                            class="table data-table Crm_table_active3 no-footer dtr-inline collapsed"
                            cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    @if (moduleStatusCheck('Branch'))
                                    <th>@lang('common.branch')</th>
                                    @endif
                                    <th>@lang('student.admission_no')</th>
                                    <th>@lang('student.name')</th>
                                    @if (!moduleStatusCheck('University') && generalSetting()->with_guardian)
                                        <th>@lang('student.father_name')</th>
                                    @endif
                                    <th>@lang('student.date_of_birth')</th>
                                    @if (moduleStatusCheck('University'))
                                        <th>@lang('university::un.semester_label')</th>
                                        <th>@lang('university::un.fac_dept')</th>
                                    @else
                                        @if(shiftEnable())
                                            <th>@lang('admin.class_Sec_shift')</th>   
                                        @else
                                            <th>@lang('admin.class_Sec')</th>   
                                        @endif
                                    @endif

                                    <th>@lang('common.gender')</th>
                                    <th>@lang('common.type')</th>
                                    <th>@lang('common.phone')</th>
                                    <th>@lang('common.actions')</th>
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
</div>
@include('backEnd.partials.data_table_js')
@include('backEnd.partials.server_side_datatable')
@push('script')
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": $.fn.dataTable.pipeline({
                    url: "{{ url('student-list-datatable') }}",
                    data: {
                        @if (moduleStatusCheck('Branch'))
                        branch_id: $('#branch_id').val() || $('#selected_branch_id').val(),
                        @endif
                        academic_year: $('#common_select_academic').val() || $('#academic_id').val(),
                        class: $('#common_select_class').val() || $('#class').val(),
                        section: $('#common_select_section').val() || $('#section').val(),
                        shift_id: $('#common_select_shift').val() || $('#shift_id').val(),
                        roll_no: $('#roll').val(),
                        name: $('#name').val(),
                        un_session_id: $('#un_session').val(),
                        un_academic_id: $('#un_academic').val(),
                        un_faculty_id: $('#un_faculty').val(),
                        un_department_id: $('#un_department').val(),
                        un_semester_label_id: $('#un_semester_label').val(),
                        un_section_id: $('#un_section').val(),
                    },
                    pages: "{{ generalSetting()->ss_page_load }}" // number of pages to cache
                }),
                columns: [
                    @if (moduleStatusCheck('Branch'))
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    @endif
                    {
                        data: 'admission_no',
                        name: 'admission_no'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    @if (!moduleStatusCheck('University') && generalSetting()->with_guardian)
                        {
                            data: 'parents.fathers_name',
                            name: 'parents.fathers_name'
                        },
                    @endif
                    {
                        data: 'dob',
                        name: 'dob'
                    },
                    @if (moduleStatusCheck('University'))
                        {
                            data: 'semester_label',
                            name: 'semester_label'
                        }, {
                            data: 'class_sec',
                            name: 'class_sec'
                        },
                    @else
                        {
                            data: 'class_sec',
                            name: 'class_sec'
                        },
                    @endif {
                        data: 'gender.base_setup_name',
                        name: 'gender.base_setup_name'
                    },
                    {
                        data: 'category.category_name',
                        name: 'category.category_name'
                    },
                    {
                        data: 'mobile',
                        name: 'sm_students.mobile'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'first_name',
                        name: 'first_name',
                        visible: false
                    },
                    {
                        data: 'last_name',
                        name: 'last_name',
                        visible: false
                    },
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
                        titleAttr: window.jsLang('export_to_pdf'),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                        orientation: "landscape",
                        pageSize: "A4",
                        margin: [0, 0, 0, 12],
                        alignment: "center",
                        header: true,
                        customize: function(doc) {
                            doc.content[1].margin = [100, 0, 100, 0]; //left, top, right, bottom
                            doc.content.splice(1, 0, {
                                margin: [0, 0, 0, 12],
                                alignment: "center",
                                image: "data:image/png;base64," + $("#logo_img").val(),
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
                }, ],
                responsive: true,
            });
        });
    </script>
@endpush
