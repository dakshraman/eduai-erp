@push('style')
    <link rel="stylesheet" href="{{ assetPath('public/css/student_fees_tabs.css') }}">
@endpush
<div role="tabpanel" class="tab-pane fade" id="fees">
    <div class="row pt-4 row-gap-24">
        <div class="col-lg-12">
            <div class="form-section">


                <div class="fees-tab">
                    <div class="fees-tab-header">
                        <div class="collaps-btn">
                            <a class="fees-extend-close" href="javascript:void(0)" data-id='#Fees1'>
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                        <div class="fees-title">
                            <div class="mr-30">
                                <input type="checkbox" name="fees[]" id="relationMother2" value="M"
                                    class="select-all common-checkbox">
                                <label for="relationMother2">Mother</label>
                            </div>
                        </div>

                    </div>
                    <div class="fees-tab-body d-none" id="Fees1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="QA_section QA_section_heading_custom check_box_table">
                                    <div class="QA_table">
                                        <table class="table data-table Crm_table_active3 no-footer dtr-inline data-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __("fees.fees_type") }}</th>
                                                    <th>{{ __("fees.due_date") }}</th>
                                                    <th class="text-right">{{ __("fees.amount") }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
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
