@extends('backEnd.master')

@section('title')
    @lang('lead::lead.lead')
@endsection
<link rel="stylesheet" href="{{ assetPath('public/backEnd/summernote/summernote.css') }}">
<style>
    .error {
        color: red;
    }
</style>
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('lead::lead.lead')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('lead::lead.lead')</a>
                    <a href="{{ route('lead.index') }}">@lang('lead::lead.lead_list')</a>

                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">

        <div class="container-fluid p-0">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-6 col-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lead::lead.select_criteria')</h3>
                    </div>
                </div>
                <div class="col-lg-4 text-md-right col-md-6 mb-30-lg col-6 text-right ">
                    @if (userPermission(1413))
                        <button class="primary-btn-small-input primary-btn small fix-gr-bg" type="button"
                            data-toggle="modal" data-target="#addLead">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </button>
                    @endif
                </div>
            </div>
            @php
                $mt = '';
                $mb = moduleStatusCheck('University') ? 'mb-25' : '';
            @endphp
            {{ html()->form('POST', route('lead.lead-search'))->attributes(['class' => 'form-horizontal', 'id' => 'lead_filter_form'])->open() }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <div class="col-lg-12 mb-25">
                                <div class="row">
                                    <div class="col-lg-2 mt-10 no-gutters input-right-icon">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('lead::lead.date_from')
                                                <span></span></label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="">
                                                            <input name="date_from" readonly
                                                                class="primary_input_field  primary_input_field date form-control {{ $errors->has('date_from') ? ' is-invalid' : '' }}"
                                                                type="text" autocomplete="off"
                                                                value="{{ isset($date_from) ? ($date_from != '' ? $date_from : '') : old('date_from') }}"
                                                                id="filter_date_from">
                                                        </div>
                                                    </div>
                                                    <button class="btn-date" data-id="#startDate" type="button">
                                                        <i class="ti-calendar" id="start-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <span class="text-danger">{{ $errors->first('date_from') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mt-10">

                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('lead::lead.date_to')
                                                <span></span></label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="">
                                                            <input name="date_to" readonly
                                                                class="primary_input_field  primary_input_field date form-control {{ $errors->has('date_to') ? ' is-invalid' : '' }}"
                                                                type="text" autocomplete="off"
                                                                value="{{ isset($date_to) ? ($date_to != '' ? $date_to : '') : old('date_to') }}"
                                                                id="filter_date_to">
                                                        </div>
                                                    </div>
                                                    <button class="btn-date" data-id="#startDate" type="button">
                                                        <i class="ti-calendar" id="start-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <span class="text-danger">{{ $errors->first('date_to') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mt-10">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('lead::lead.source')
                                                <span></span></label>
                                            <select name="source"
                                                class="primary_select  form-control {{ $errors->has('select_source') ? ' is-invalid' : '' }}"
                                                id="filter_source">
                                                <option data-display="@lang('lead::lead.select_source')" value="">
                                                    @lang('lead::lead.select_source')
                                                </option>
                                                @foreach ($sources as $source)
                                                    <option value="{{ @$source->id }}"
                                                        {{ isset($source_id) ? ($source_id == $source->id ? 'selected' : '') : '' }}>
                                                        {{ @$source->source_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('select_source'))
                                                <span class="text-danger invalid-select" role="alert">
                                                    {{ $errors->first('select_source') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="col-lg-3 mt-10">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('lead::lead.status')
                                                <span></span></label>
                                            <select class="primary_select  form-control" name="status" id="filter_status">
                                                <option data-display="@lang('lead::lead.select_status')" value="">
                                                    @lang('lead::lead.select_status')
                                                </option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status->id }}"
                                                        {{ isset($status_id) ? ($status_id == $status->id ? 'selected' : '') : '' }}>
                                                        {{ $status ? $status->status_name : '' }}</option>
                                                @endforeach


                                            </select>
                                            @if ($errors->has('select_status'))
                                                <span class="text-danger invalid-select" role="alert">
                                                    {{ $errors->first('select_status') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mt-10">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('lead::lead.assign')
                                                <span></span></label>
                                            <select class="primary_select  form-control" name="filter_assign_id"
                                                id="filter_assign_id">
                                                <option data-display="@lang('lead::lead.assign')" value="">
                                                    @lang('lead::lead.assign')
                                                </option>
                                                @foreach ($staffs as $staff)
                                                    <option value="{{ $staff->id }}"
                                                        {{ isset($staff_id) ? ($staff_id == $staff->id ? 'selected' : '') : '' }}>
                                                        {{ $staff ? $staff->full_name : '' }}</option>
                                                @endforeach


                                            </select>
                                            @if ($errors->has('select_status'))
                                                <span class="text-danger invalid-select" role="alert">
                                                    {{ $errors->first('select_status') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (moduleStatusCheck('University'))
                                @php
                                    $mt = 'mt-25';
                                @endphp

                                @includeIf(
                                    'university::common.session_faculty_depart_academic_semester_level',
                                    [
                                        'mt' => $mt,
                                
                                        'hide' => ['USUB', 'USEC'],
                                    ]
                                )
                            @else
                                <div class="col-lg-3 mt-10">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">@lang('common.class')
                                            <span></span></label>
                                        <select
                                            class="primary_select  form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                                            name="class" id="classSelectStudent">
                                            <option data-display="@lang('common.select_class') " value="">@lang('common.select_class')
                                                <span class="text-danger"></span>
                                            </option>
                                            @foreach ($classes as $item)
                                                <option value="{{ $item->id }}">{{ $item->class_name }}</option>
                                            @endforeach

                                        </select>
                                        <div class="pull-right loader loader_style" id="select_class_loader">
                                            <img class="loader_img_style"
                                                src="{{ assetPath('public/backEnd/img/demo_wait.gif') }}" alt="loader">
                                        </div>

                                        @if ($errors->has('class'))
                                            <span class="text-danger invalid-select" role="alert">
                                                {{ $errors->first('class') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-3 {{ $mt != '' ? $mt : 'mt-10' }}">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="">@lang('lead::lead.city')
                                        <span></span></label>
                                    <select
                                        class="primary_select  form-control {{ $errors->has('select_status') ? ' is-invalid' : '' }}"
                                        name="city" id="filter_city">
                                        <option data-display="@lang('lead::lead.city')" value="">
                                            @lang('lead::lead.city')
                                        </option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ isset($city_id) ? ($city_id == $city->id ? 'selected' : '') : '' }}>
                                                {{ $city->city_name }}</option>
                                        @endforeach


                                    </select>
                                    @if ($errors->has('city'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('city') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12 mt-20 text-right">
                                <button type="submit" class="primary-btn small fix-gr-bg">
                                    <span class="ti-search pr-2"></span>
                                    @lang('lead::lead.search')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ html()->form()->close() }}
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lead::lead.lead_list')</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <x-table>
                                <table id="table_id" class="table data-table" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            @foreach ($active_fields as $field)
                                                <th>{{ __('lead::lead.' . $field->field_name) }}</th>
                                            @endforeach

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
    </section>

    <div class="modal fade admin-query" id="addLead">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('lead::lead.lead')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                {{ html()->form('POST', route('lead.store'))->attributes(['class' => 'form-horizontal', 'files' => true, 'enctype' => 'multipart/form-data', 'id' => 'createLeadForm'])->open() }}
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">

                                        <div class="col-lg-4">
                                            <label class="primary_input_label" for="">@lang('admin.source')
                                                <span>*</span></label>
                                            <select class="primary_select " name="source_id">
                                                <option data-display="@lang('admin.source')*" value="">
                                                    @lang('admin.source')*</option>
                                                @foreach ($sources as $source)
                                                    <option value="{{ @$source->id }}">{{ @$source->source_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @if ($errors->has('source_id'))
                                                <span class="text-danger invalid-select" role="alert"
                                                    style="display: block">
                                                    {{ $errors->first('source_id') }}
                                                </span>
                                            @endif


                                        </div>
                                        <div class="col-lg-4">
                                            <label class="primary_input_label" for="">@lang('lead::lead.status')
                                                <span>*</span></label>
                                            <select class="primary_select " name="status_id">
                                                <option data-display="@lang('lead::lead.status') *" value="">
                                                    @lang('lead::lead.status') *
                                                </option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ @$status->id }}">{{ @$status->status_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @if ($errors->has('status_id'))
                                                <span class="text-danger invalid-select" role="alert"
                                                    style="display: block">
                                                    {{ $errors->first('status_id') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="primary_input_label" for="">@lang('lead::lead.assign')
                                                <span>*</span></label>
                                            <select class="primary_select " name="assign_id">
                                                <option data-display="@lang('lead::lead.assign')" value="">
                                                    @lang('lead::lead.assign')
                                                </option>
                                                @foreach ($staffs as $staff)
                                                    <option value="{{ @$staff->id }}">{{ @$staff->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @if ($errors->has('assign_id'))
                                                <span class="text-danger invalid-select" role="alert"
                                                    style="display: block">
                                                    {{ $errors->first('assign_id') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-25">
                                    <div class="row">

                                        @if (moduleStatusCheck('University'))
                                            @php
                                                $mt = 'mt-25';
                                            @endphp
                                            @includeIf(
                                                'university::common.session_faculty_depart_academic_semester_level',
                                                [
                                                    'mt' => $mt,
                                                    'required' => ['USN', 'UF', 'UD', 'UA', 'US', 'USL'],
                                                    'hide' => ['USUB', 'USEC'],
                                                ]
                                            )
                                        @else
                                            <div class="col-lg-4 ">
                                                <div class="primary_input ">
                                                    <label class="primary_input_label" for="">@lang('common.class')
                                                        <span>*</span></label>
                                                    <select
                                                        class="primary_select  form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                                                        name="class" id="leadClass">
                                                        <option data-display="@lang('common.select_class') *" value="">
                                                            @lang('common.select_class') <span class="text-danger"> *</span></option>
                                                        @foreach ($classes as $item)
                                                            <option value="{{ $item->id }}">{{ $item->class_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pull-right loader loader_style"
                                                        id="select_lead_class_loader">
                                                        <img class="loader_img_style"
                                                            src="{{ assetPath('public/backEnd/img/demo_wait.gif') }}"
                                                            alt="loader">
                                                    </div>

                                                    @if ($errors->has('class'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('class') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-lg-4 {{ $mt }}">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('lead::lead.first_name')<span
                                                        class="text-danger"> *</span></label>
                                                <input
                                                    class="primary_input_field form-control{{ @$errors->has('first_name') ? ' is-invalid' : '' }}"
                                                    type="text" name="first_name">


                                                @if ($errors->has('first_name'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('first_name') }}</span>
                                                @endif
                                                {{-- @if ($errors->has('first_name'))
                                                    <div class="error">{{ $errors->first('first_name') }}</div>
                                                @endif --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-4 {{ $mt }}">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('lead::lead.last_name')
                                                    <span></span> </label>
                                                <input
                                                    class="primary_input_field form-control{{ @$errors->has('last_name') ? ' is-invalid' : '' }}"
                                                    type="text" name="last_name" id="last_name">



                                                @if ($errors->has('last_name'))
                                                    <span class="text-danger" style="display: block">
                                                        {{ $errors->first('last_name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12 mt-25">
                                    <div class="row">

                                        <div class="col-lg-3">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('admin.phone')<span
                                                        class="text-danger"> *</span></label>
                                                <input oninput="phoneCheck(this)"
                                                    class="primary_input_field  form-control {{ @$errors->has('phone') ? ' is-invalid' : '' }}"
                                                    type="text" name="phone" id="phone">


                                                @if ($errors->has('phone'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('phone') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="primary_input">
                                                <label class="primary_input_label"
                                                    for="">@lang('admin.email')</label>

                                                <input oninput="emailCheck(this)"
                                                    class="primary_input_field read-only-input form-control"
                                                    type="email" name="email">

                                                @if ($errors->has('email'))
                                                    <span class="text-danger" style="display: block">
                                                        {{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">

                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('admin.date_of_birth')
                                                </label>

                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input
                                                                    class="primary_input_field  primary_input_field date form-control form-control"
                                                                    id="startDate" type="text" name="date_of_birth"
                                                                    readonly="true" value="{{ date('m/d/Y') }}" required>
                                                            </div>
                                                        </div>
                                                        <button class="btn-date" data-id="#startDate" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <span class="text-danger">{{ $errors->first('date') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="primary_input ">
                                                <label class="primary_input_label"
                                                    for="">@lang('lead::lead.city')</label>

                                                <select
                                                    class="primary_select  form-control{{ $errors->has('city') ? ' is-invalid' : '' }}"
                                                    name="city">
                                                    <option data-display="@lang('lead::lead.city')" value="">
                                                        @lang('lead::lead.city')</option>
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}"
                                                            {{ old('city') == $city->id ? 'selected' : '' }}>
                                                            {{ $city->city_name }}</option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('city'))
                                                    <span class="text-danger invalid-select" role="alert"
                                                        style="display: block">
                                                        {{ $errors->first('city') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-25">
                                    @include('lead::lead._lead_custom_field')
                                </div>
                                <div class="col-lg-12 mt-25">
                                    <div class="row">
                                        <div class="col-lg-12">

                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('common.description')
                                                </label>
                                                <textarea class="primary_input_field a form-control" cols="0" rows="10" name="description"
                                                    id="summernote">{{ old('description') }}</textarea>


                                                @if ($errors->has('description'))
                                                    <span
                                                        class="error text-danger">{{ $errors->first('description') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-12 text-center mt-20">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button type="button" class="primary-btn tr-bg"
                                            data-dismiss="modal">@lang('admin.cancel')</button>
                                        <button class="primary-btn fix-gr-bg submit" id="save_button_query"
                                            type="submit">@lang('admin.save')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                {{ html()->form()->close() }}

            </div>
        </div>
    </div>

@endsection
@include('backEnd.partials.data_table_js')
@include('backEnd.partials.date_picker_css_js')
@section('script')
    @include('backEnd.partials.server_side_datatable')
    <script src="{{ assetPath('modules/lead/js/app.js') }}"></script>
    <script src="{{ assetPath('public/backEnd/summernote/summernote.js') }}"></script>


    <script>
        $(document).ready(function() {

            let lead_table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": $.fn.dataTable.pipeline({
                    url: "{{ route('lead.lead-list-datatable') }}",
                    pages: 2, // number of pages to cache
                    data: function(d) {
                        d.date_from = $("#filter_date_from").val();
                        d.date_to = $("#filter_date_to").val();
                        d.source = $("#filter_source").val();
                        d.status = $("#filter_status").val();
                        d.filter_assign_id = $("#filter_assign_id").val();

                        d.city = $("#filter_city").val();

                        @if (moduleStatusCheck('University'))
                            d.un_session_id = $("#lead_filter_form #select_session").val();
                            d.un_faculty_id = $("#lead_filter_form #select_faculty").val();
                            d.un_department_id = $("#lead_filter_form #select_dept").val();
                            d.un_academic_id = $("#lead_filter_form #select_academic").val();
                            d.un_semester_id = $("#lead_filter_form #select_semester").val();
                            d.un_semester_label_id = $(
                                "#lead_filter_form #select_semester_label").val();
                            d.un_section_id = $("#lead_filter_form #select_section").val();
                        @else
                            // d.session = $("#academic_year").val();
                            d.class = $("#classSelectStudent").val();
                        @endif
                    }
                }),
                columns: [
                    @if (is_showing('sl'))
                        {
                            data: 'serial_number',
                            name: 'serial_number'
                        },
                    @endif
                    @if (is_showing('name'))
                        {
                            data: 'first_name',
                            name: 'first_name'
                        },
                    @endif
                    @if (is_showing('created_date'))
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                    @endif
                    @if (is_showing('phone'))
                        {
                            data: 'mobile',
                            name: 'mobile'
                        },
                    @endif
                    @if (is_showing('email'))
                        {
                            data: 'email',
                            name: 'email'
                        },
                    @endif
                    @if (is_showing('class'))
                        {
                            data: 'class',
                            @if (moduleStatusCheck('University'))
                                name: 'unDepartment.name'
                            @else
                                name: 'leadClass.class_name'
                            @endif
                        },
                    @endif
                    @if (is_showing('source'))
                        {
                            data: 'source',
                            name: 'source.source_name'
                        },
                    @endif
                    @if (is_showing('assign'))
                        {
                            data: 'assign',
                            name: 'assign.full_name'
                        },
                    @endif
                    @if (is_showing('status'))
                        {
                            data: 'status',
                            name: 'status.status_name'
                        },
                    @endif
                    @if (is_showing('actions'))
                        {
                            data: 'actions',
                            name: 'actions',
                            sortable: false,
                            searchable: false
                        },
                    @endif


                ],
                "stateSave": true,
                bLengthChange: false,
                bDestroy: true,
                language: {
                    search: "<i class='ti-search'></i>",
                    searchPlaceholder: "Quick Search",
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
                        titleAttr: "Copy",
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "excelHtml5",
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: "Excel",
                        title: $("#logo_title").val(),
                        margin: [10, 10, 10, 0],
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "csvHtml5",
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: "CSV",
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        title: $("#logo_title").val(),
                        titleAttr: "PDF",
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                        orientation: "landscape",
                        pageSize: "A4",
                        margin: [0, 0, 0, 12],
                        alignment: "center",
                        header: true,
                        customize: function(doc) {
                            doc.content[1].margin = [100, 0, 100,
                                0
                            ]; //left, top, right, bottom
                            doc.content.splice(1, 0, {
                                margin: [0, 0, 0, 12],
                                alignment: "center",
                                image: "data:image/png;base64," + $("#logo_img")
                                    .val(),
                            });
                        },
                    },
                    {
                        extend: "print",
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: "Print",
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
                order: [
                    [2, 'desc']
                ],
                drawCallback: function(settings) {
                    $('.statusChange').niceSelect('destroy').niceSelect();
                    $.each($('.statusChange'), function(i, v) {
                        let color = $(this).find(':selected').data('color');
                        $(this).parent().find('.current').css('color', color);
                    })
                },
            });




            $('#lead_filter_form').on('submit', function(e) {
                e.preventDefault();
                lead_table.clearPipeline();
                lead_table.ajax.reload(null, false);
            })

            $.each($('.statusChange'), function(i, v) {
                let color = $(this).find(':selected').data('color');
                $(this).parent().find('.current').css('color', color);
            })

            $("#lead_academic_year").on(
                "change",
                function() {
                    var url = $("#url").val();
                    var i = 0;
                    // alert('okay');
                    var formData = {
                        id: $(this).val(),
                    };


                    // get section for student
                    $.ajax({
                        type: "GET",
                        data: formData,
                        dataType: "json",
                        url: url + "/" + "academic-year-get-class",

                        beforeSend: function() {
                            $('#select_lead_class_loader').removeClass('loader').addClass(
                                'pre_loader');
                        },

                        success: function(data) {
                            $("#leadClass").empty().append(
                                $("<option>", {
                                    value: '',
                                    text: window.jsLang('select_class') + ' *',
                                })
                            );

                            if (data[0].length) {
                                $.each(data, function(i, className) {
                                    $("#leadClass").append(
                                        $("<option>", {
                                            value: className.id,
                                            text: className.class_name,
                                        })
                                    );
                                });
                            }
                            $('#leadClass').niceSelect('update');
                            $('#leadClass').trigger('change');
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        },
                        complete: function() {
                            i--;
                            if (i <= 0) {
                                $('#select_lead_class_loader').removeClass('pre_loader').addClass(
                                    'loader');
                            }
                        }
                    });
                }
            );
        });

        $('#table_id').on('change', '.statusChange', function() {
            var url = $("#url").val();
            let lead_id = $(this).data('lead_id');
            let status_id = $(this).val();
            let color = $(this).find(':selected').data('color');

            $(this).parent().find('.current').css('color', color);

            var formData = {
                lead_id: lead_id,
                status_id: status_id,

            };
            $.ajax({

                type: "post",
                data: formData,
                dataType: "html",
                url: url + "/lead/change-status",

                success: function(data) {

                    setTimeout(function() {
                        toastr.success("Status Change Operation Successfully", "Success", {
                            timeOut: 5000,
                        });
                    }, 500);

                },
                error: function(data) {

                    setTimeout(function() {
                        toastr.error("Operation Not Done!", "Error Alert", {
                            timeOut: 5000,
                        });
                    }, 500);
                },

            });



        })
    </script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Description',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    // ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    // ['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen' /*, 'codeview' */ ]], // remove codeview button
                    ['help', ['help']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        sendFile(files, '#summernote')
                    }
                }
            });
        });
    </script>
    <script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>
@endsection
