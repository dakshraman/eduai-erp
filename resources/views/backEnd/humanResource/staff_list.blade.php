@extends('backEnd.master')
@section('title')
    @lang('hr.staff_list')
@endsection
@section('mainContent')
    @push('css')
        <style type="text/css">
            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 34px;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked+.slider {
                background: var(--primary-color);
            }

            input:focus+.slider {
                box-shadow: 0 0 1px linear-gradient(90deg, var(--gradient_1) 0%, #c738d8 51%, var(--gradient_1) 100%);
            }

            input:checked+.slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            /* Rounded sliders */
            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }

            /* th,td{
                font-size: 9px !important;
                padding: 5px !important

            } */
            /* table search and export option */
            .QA_section .QA_table .dt-container .dt-search{
                top: -140px;
            }

            .student_list_list_layout.hide .QA_section .QA_table div.dt-buttons {
                top: -112px !important;
                right: 20px;
            }

            html[dir="rtl"] .student_list_list_layout.hide .QA_section .QA_table div.dt-buttons {
                top: -112px !important;
                left: 20px;
                right: auto;
            }


            .student_list_list_layout:not(.hide) .QA_section .QA_table div.dt-buttons {
                top: -132px !important;
            }

            html[dir="rtl"] .student_list_list_layout:not(.hide) .QA_section .QA_table div.dt-buttons {
                top: -132px !important;
            }

            .student_list_list_layout.hide .dt-container > *:not(.dt-buttons){
                display: none!important;
            }

            .student_list_grid_layout.hide{
                display: none!important;
            }

            .student_list_grid_layout.show {
                display: block!important;
            }

            @media (max-width: 1440px) {
                .student_list_list_layout .QA_section .QA_table .dt-container .dt-search {
                    left: 45% !important;
                }
            }

            @media (max-width: 767px) {
                .student_list_list_layout .QA_section .QA_table .dt-container .dt-search {
                    left: calc(100% - 150px) !important;
                    min-width: 150px!important;
                    width: 150px!important;
                    top: -140px!important;
                }
            }

            @media (max-width: 479px) {
                .student_list_list_layout .QA_section .QA_table .dt-container .dt-search {
                    left: calc(100% - 120px) !important;
                    min-width: 120px!important;
                    width: 120px!important;
                    top: -140px!important;
                }
            }

        </style>
    @endpush
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('hr.staff_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('hr.human_resource')</a>
                    <a href="#">@lang('hr.staff_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-8 col-md-6 col-sm-6">
                                <div class="main-title xs_mt_0 mt_0_sm">
                                    <h3 class="mb-15">@lang('common.select_criteria') </h3>
                                </div>
                            </div>
            
                            @if (userPermission('addStaff'))
                                <div class="col-lg-4 text-sm-right text-left col-md-6 mb-30-lg col-sm-6 text_sm_right mb-4 mb-sm-0">
                                    <a href="{{ route('addStaff') }}" class="primary-btn small fix-gr-bg">
                                        <span class="ti-plus pr-2"></span>
                                        @lang('hr.add_staff')
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if(generalSetting()->staff_grid_view == 1) {{ html()->form('GET', route('staffGrid'))->open() }} @else {{ html()->form('GET', route('searchStaff'))->open() }} @endif
                        <div class="row">
                            <input type="hidden" name="role_id" id="role_id" value="{{ @$data['role_id'] }}">
                            <input type="hidden" name="staff_no" id="staff_no" value="{{ @$data['staff_no'] }}">
                            <input type="hidden" name="staff_name" id="staff_name" value="{{ @$data['staff_name'] }}">
                            @if (moduleStatusCheck('Branch'))
                                <input type="hidden" name="branch_id" id="branch_id" value="{{ @$data['branch_id'] }}">
                                @include('branch::components.branch_select', [
                                    'grid_class' => 'col-lg-4',
                                    'mb' => 'mb-20',
                                    'branch_id' => @$data['branch_id'] ? @$data['branch_id'] : '',
                                ])                
                            @endif
                            <div class="col-lg-4">
                                <label class="primary_input_label" for="">
                                    {{ __('common.role') }}
                                    <span class="text-danger"> </span>
                                </label>
                                <select class="primary_select  form-control" name="role_id" id="role_id">
                                    <option data-display="@lang('hr.role')" value=""> @lang('common.select') </option>
                                    @foreach ($roles as $key => $value)
                                        <option value="{{ $value->id }}"
                                            @if (isset($data['role_id']) && $value->id == $data['role_id']) selected @endif>{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 mt-30-md">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="">
                                        {{ __('hr.search_by_staff_id') }}
                                        <span class="text-danger"> </span>
                                    </label>
                                    <input class="primary_input_field" type="text" placeholder=" @lang('hr.search_by_staff_id')"
                                        name="staff_no" value="{{ @$data['staff_no'] }}">

                                </div>
                            </div>
                            <div class="col-lg-4 mt-30-md">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="">
                                        {{ __('common.search_by_name') }}
                                        <span class="text-danger"> </span>
                                    </label>
                                    <input class="primary_input_field" type="text" placeholder="@lang('common.search_by_name')"
                                        name="staff_name" value="{{ @$data['staff_name'] }}">

                                </div>
                            </div>
                            <div class="col-lg-12 mt-20 text-right">
                                <button type="submit" class="primary-btn small fix-gr-bg">
                                    <span class="ti-search pr-2"></span>
                                    @lang('common.search')
                                </button>
                            </div>
                        </div>
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
            @if(generalSetting()->staff_grid_view == 1)
            <div class="d-flex align-items-center justify-content-between mt-4 white-box position-relative">
                <div class="main-title">
                    <h3 class="mb-0">@lang('hr.staff_list')</h3>
                </div>

                <div class="student_grid_search_option">
                    <label for="student_grid_search" class="student_grid_search_option_label">
                        <i class="ti-search"></i>
                        <input type="search" name="" placeholder="Quick Search" class="student_grid_search_option_input" id="student_grid_search">
                    </label>
                </div>
            </div>
            @endif
            <section class="view_layout_toggler ml-auto">
                <button class="view_layout_toggler_item grid {{ generalSetting()->staff_grid_view == 1 ? 'active' : '' }}" id="grid">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.75 7.5H5.25C6.75 7.5 7.5 6.75 7.5 5.25V3.75C7.5 2.25 6.75 1.5 5.25 1.5H3.75C2.25 1.5 1.5 2.25 1.5 3.75V5.25C1.5 6.75 2.25 7.5 3.75 7.5Z"
                            stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"
                        />
                        <path
                            d="M12.75 7.5H14.25C15.75 7.5 16.5 6.75 16.5 5.25V3.75C16.5 2.25 15.75 1.5 14.25 1.5H12.75C11.25 1.5 10.5 2.25 10.5 3.75V5.25C10.5 6.75 11.25 7.5 12.75 7.5Z"
                            stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"
                        />
                        <path
                            d="M12.75 16.5H14.25C15.75 16.5 16.5 15.75 16.5 14.25V12.75C16.5 11.25 15.75 10.5 14.25 10.5H12.75C11.25 10.5 10.5 11.25 10.5 12.75V14.25C10.5 15.75 11.25 16.5 12.75 16.5Z"
                            stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"
                        />
                        <path
                            d="M3.75 16.5H5.25C6.75 16.5 7.5 15.75 7.5 14.25V12.75C7.5 11.25 6.75 10.5 5.25 10.5H3.75C2.25 10.5 1.5 11.25 1.5 12.75V14.25C1.5 15.75 2.25 16.5 3.75 16.5Z"
                            stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"
                        />
                    </svg>
                </button>
                <button class="view_layout_toggler_item list {{ generalSetting()->staff_grid_view == 0 ? 'active' : '' }}" id="list">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.25 14.625H15.75" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.25 9.375H15.75" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.25 4.125H15.75" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.25 4.125L3 4.875L5.25 2.625" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.25 9.375L3 10.125L5.25 7.875" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.25 14.625L3 15.375L5.25 13.125" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </section>
            {{-- List View --}}
            @if(generalSetting()->staff_grid_view == 0)
                @include('backEnd.humanResource.partial.list')
            @endif
        </div>
    </section>
    {{-- Grid View --}}
    
    @if(generalSetting()->staff_grid_view == 1)
        @include('backEnd.humanResource.partial.grid')
    @endif
    {{-- deleteStaffModal --}}
    <div class="modal fade admin-query" id="deleteStaffModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('hr.Confirmation Required') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">  
                    <div class="text-center">
                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                    </div>
                    <div class="mt-40 d-flex justify-content-between">

                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
    
                        {{ html()->form('POST', route('delete_staff'))->attribute('enctype', 'multipart/form-data')->open() }}
                        <input type="hidden" name="id" value="">
                        <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')  
<script>
    function deleteStaff(id){
        var modal = $('#deleteStaffModal');
        modal.find('input[name=id]').val(id)
        modal.modal('show');
    }
</script>
<script>
    $(document).ready(function() {

        $('#grid').click(function() {
            updateView(1);
        });

        $('#list').click(function() {
            updateView(0);
        });

        function updateView(value) {
            $.ajax({
                url: "{{ route('staff_view_toggle') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    staff_grid_view: value
                },
                success: function(response) {
                    if(response.success){
                        window.location.href = "{{ route('staff_directory') }}";
                        toastr.success("Staff View Mood Change Successfully", "Successful");
                    } else {
                        toastr.error("Something Wrong", "Error");
                    }
                },
                error: function() {
                    toastr.error("An error occurred. Please try again", "Error");
                }
            });
        }
    });
</script>
@endpush
