@push('style')
    <link rel="stylesheet" href="{{ url('Modules\Fees\Resources\assets\css\feesStyle.css') }}" />
    <link rel="stylesheet" href="{{ assetPath('public/css/student_fees_tabs.css') }}">
@endpush
<div role="tabpanel" class="tab-pane fade" id="fees">
    <div class="row pt-4 row-gap-24">
        <div class="col-lg-12">
            <div class="form-section">
                  <div class="row">
                        <div class="col-lg-3">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                    <div class="white-box">

                                        <div class="main-title">
                                            <h3 class="mb-15"> @lang('common.add')  </h3>
                                        </div>

                                        <div class="add-visitor">
                                            <div class="row">
                                                <div class="col-lg-12 d-flex">
                                                    <div class="d-flex flex-wrap" id="showValue"></div>
                                                    <input type="hidden" id="fees_invoice_prefix"
                                                        value="{{ @$invoiceSettings->prefix }}">
                                                </div>
                                            </div>

                                            <div class="row mt-15">

                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label"
                                                            for="">@lang('fees.create_date') <span class="text-danger">  *</span></label>
                                                        <div class="primary_datepicker_input">
                                                            <div class="no-gutters input-right-icon">
                                                                <div class="col">
                                                                    <div class="">
                                                                        <input
                                                                            class="primary_input_field  primary_input_field date form-control form-control{{ $errors->has('create_date') ? ' is-invalid' : '' }}"
                                                                            id="create_date" type="text"
                                                                            name="create_date"
                                                                            value="{{ date('m/d/Y') }}">
                                                                    </div>
                                                                </div>
                                                                <button class="btn-date" data-id="#create_date"
                                                                    type="button">
                                                                    <label for="create_date">
                                                                        <i class="ti-calendar" id="create_date"></i>
                                                                    </label>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('create_date'))
                                                            <span class="text-danger invalid-select" role="alert">
                                                                {{ $errors->first('create_date') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row mt-15">

                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label"
                                                            for="">@lang('fees.due_date') <span
                                                                class="text-danger"> *</span></label>
                                                        <div class="primary_datepicker_input">
                                                            <div class="no-gutters input-right-icon">
                                                                <div class="col">
                                                                    <div class="">
                                                                        <input
                                                                            class="primary_input_field  primary_input_field date form-control form-control{{ $errors->has('due_date') ? ' is-invalid' : '' }}"
                                                                            id="due_date" type="text"
                                                                            name="due_date"
                                                                            value="{{ isset($invoiceInfo) ? date('m/d/Y', strtotime($invoiceInfo->due_date)) : date('m/d/Y') }}">
                                                                    </div>
                                                                </div>
                                                                <button class="btn-date" data-id="#due_date"
                                                                    type="button">
                                                                    <label for="due_date">
                                                                        <i class="ti-calendar" id="due_date"></i>
                                                                    </label>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('due_date'))
                                                            <span class="text-danger invalid-select" role="alert">
                                                                {{ $errors->first('due_date') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-15 {{ isset($invoiceInfo) ? 'd-none' : '' }}">
                                                <div class="col-lg-12">
                                                    <label class="primary_input_label" for="">
                                                        {{ __('fees.payment_status') }}
                                                        <span class="text-danger"> *</span>
                                                    </label>
                                                    <select
                                                        class="primary_select  form-control{{ $errors->has('payment_status') ? ' is-invalid' : '' }}"
                                                        name="payment_status" id="paymentStatus">
                                                        <option data-display="@lang('fees.payment_status') *" value="">
                                                            @lang('fees.payment_status') *</option>
                                                        <option value="not"
                                                            {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'not' ? 'selected' : '') : (old('payment_status') == 'not' ? 'selected' : '') }}>
                                                            @lang('fees.not_paid')</option>
                                                        <option value="partial"
                                                            {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'partial' ? 'selected' : '') : (old('payment_status') == 'partial' ? 'selected' : '') }}>
                                                            @lang('fees.partial_paid')</option>
                                                        <option value="full"
                                                            {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'full' ? 'selected' : '') : (old('payment_status') == 'full' ? 'selected' : '') }}>
                                                            @lang('fees.full_paid')</option>
                                                    </select>
                                                    @if ($errors->has('payment_status'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('payment_status') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row mt-15 d-none" id="paymentMethod">
                                                <div class="col-lg-12">
                                                    <label class="primary_input_label" for="">
                                                        {{ __('fees.payment_method') }}
                                                        <span class="text-danger"> *</span>
                                                    </label>
                                                    <select
                                                        class="primary_select  form-control{{ $errors->has('payment_method') ? ' is-invalid' : '' }}"
                                                        name="payment_method" id="paymentMethodName">
                                                        <option data-display="@lang('fees.payment_method')*" value="">
                                                            @lang('fees.payment_method')*</option>
                                                        @foreach ($paymentMethods as $paymentMethod)
                                                            <option value="{{ $paymentMethod->method }}"
                                                                {{ isset($invoiceInfo) ? ($invoiceInfo->payment_method == $paymentMethod->method ? 'selected' : '') : (old('payment_method') == $paymentMethod->method ? 'selected' : '') }}>
                                                                {{ $paymentMethod->method }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('payment_method'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('payment_method') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row mt-15 d-none" id="bankPayment">
                                                <div class="col-lg-12">
                                                    <label class="primary_input_label" for="">
                                                        {{ __('fees.bank') }}
                                                        <span class="text-danger"> *</span>
                                                    </label>
                                                    <select
                                                        class="primary_select  form-control{{ $errors->has('bank') ? ' is-invalid' : '' }}"
                                                        name="bank">
                                                        <option data-display="@lang('fees.select_bank')*" value="">
                                                            @lang('fees.select_bank')*</option>
                                                        @foreach ($bankAccounts as $bankAccount)
                                                            <option value="{{ $bankAccount->id }}"
                                                                {{ isset($invoiceInfo) ? ($invoiceInfo->bank_id == $bankAccount->id ? 'selected' : '') : (old('bank') == $bankAccount->id ? 'selected' : '') }}>
                                                                {{ $bankAccount->bank_name }}
                                                                ({{ $bankAccount->account_number }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('bank'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('bank') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @php
                                                $tooltip = '';

                                            @endphp
                                            <input type="hidden" value="{{ @$invoiceInfo->id }}"
                                                id="newFeesEditId">

                                            <div class="row mt-40">
                                                <div class="col-lg-12 text-center">
                                                    <button type="submit"
                                                        class="primary-btn fix-gr-bg submit fmInvoice"
                                                        data-tooltip="tooltip" title="{{ $tooltip }}">
                                                        <span class="ti-check"></span>
                                                        @if (isset($invoiceInfo))
                                                            @lang('common.update')
                                                        @else
                                                            @lang('common.save')
                                                        @endif
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-9">
                            <div class="white-box">
                                <div class="row">
                                    <div class="col-lg-4 no-gutters">
                                        <div class="main-title">
                                            <h3 class="mb-15">@lang('fees.fees_type_list')</h3>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <div class="pb-0 fees_invoice_type_div">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <select
                                                        class="primary_select  form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}"
                                                        id="selectFeesType" name="fees_type">
                                                        <option data-display="@lang('fees.fees_type') *" value="">
                                                            @lang('fees.fees_type') *</option>
                                                        <option value="" disabled>@lang('fees.fees_group')</option>
                                                        @foreach ($feesGroups as $feesGroup)
                                                            <option value="grp{{ $feesGroup->id }}">
                                                                {{ $feesGroup->name }}
                                                            </option>
                                                        @endforeach
                                                        <option value="" disabled>@lang('fees.fees_type')</option>
                                                        @foreach ($feesTypes as $feesType)
                                                            <option value="typ{{ $feesType->id }}">
                                                                {{ $feesType->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('fees_type'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('fees_type') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mt-20">
                                                <div class="col-lg-12 justify-content-end d-flex">
                                                    <div class="text-right">
                                                        <input type="checkbox" name="singleInvoice"
                                                            id="singleInvoice" class="common-checkbox form-control"
                                                            value="1">
                                                        <label for="singleInvoice">@lang('fees::feesModule.group_fees_generate_seperate_invoice')</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-20">
                                                <div class="col-lg-12 justify-content-end d-flex">
                                                    <div class="text-right">
                                                        <input type="checkbox" id="cloneAmount"
                                                            class="common-checkbox form-control permission-checkAll">
                                                        <label for="cloneAmount">@lang('fees::feesModule.clone_amount')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" class="weaverType" value="amount">
                                        <div class="big-table">
                                            <table class="table school-table-style fees_invoice_type_table"
                                                cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('common.sl')</th>
                                                        <th>@lang('fees.fees_type')</th>
                                                        <th>@lang('accounts.amount')</th>
                                                        <th>@lang('fees.waiver')</th>
                                                        <th>@lang('fees.sub_total')</th>
                                                        <th>@lang('common.action')</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="allFeesTypes">
                                                  
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>@lang('exam.result')</td>
                                                        <td></td>
                                                        <td class="showTotalAmount">{{ currency_format(0) }}</td>
                                                        <td class="showTotalWeaver">{{ currency_format(0) }}</td>
                                                        <td class="showSubTotalDiscount">{{ currency_format(0) }}</td>
                                                       
                                                        <td></td>
                                                        <input class="totalPaidAmount" type="hidden"
                                                            name="total_paid_amount">
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script type="text/javascript" src="{{ url('Modules\Fees\Resources\assets\js\app.js') }}"></script>
    <script>
        selectPosition({!! feesInvoiceSettings()->invoice_positions !!});
    </script>
    <script> const decimal_digits = {{ generalSetting()->currencyDetail->decimal_digit ?? 0 }};  </script>

@endpush