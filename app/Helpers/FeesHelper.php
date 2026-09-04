<?php

/**
 * FeesHelper.php — Fees, payment & finance helpers.
 *
 * Extracted from Helper.php (P4 DRY cleanup).
 * Registered in composer.json autoload.files — already loaded globally.
 *
 * Contains: fees payment, invoice generation, carry-forward, discounts,
 * income recording, payment method resolution.
 */

use App\Models\DirectFeesInstallmentAssign;
use App\Models\DirectFeesReminder;
use App\Models\FeesCarryForwardLog;
use App\Models\FeesCarryForwardSettings;
use App\Models\FeesInvoice;
use App\Models\SmAddIncome;
use App\Models\SmFeesCarryForward;
use App\Models\SmFeesMaster;
use App\Models\SmFeesPayment;
use App\Models\SmPaymentGatewaySetting;
use App\Models\SmPaymentMethhod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesInvoiceSettings;
use Modules\Fees\Entities\FmFeesType;
use Modules\University\Entities\UnFeesInstallmentAssign;

if (! function_exists('chargeAmount')) {
    function chargeAmount(string $gateway, $amount)
    {
        $gatewaySettings = SmPaymentGatewaySetting::where('gateway_name', $gateway)->where('school_id', auth()->user()->school_id)->first();
        $chargeAmount = 0;
        if ($gatewaySettings && $gatewaySettings->service_charge && $amount) {
            if ($gatewaySettings->charge_type === 'P') {
                $chargeAmount = number_format(($gatewaySettings->charge / 100) * $amount, 2, '.', '');
            } elseif ($gatewaySettings->charge_type === 'F') {
                $chargeAmount = number_format($gatewaySettings->charge, 2, '.', '');
            }
        }

        return $chargeAmount;
    }
}

if (! function_exists('serviceChargeWithTotal')) {
    function serviceChargeWithTotal(string $gateway, $amount = null)
    {
        $charge = 0;
        $gatewaySettings = SmPaymentGatewaySetting::where('gateway_name', $gateway)->where('school_id', auth()->user()->school_id)->first();
        if ($gatewaySettings && $gatewaySettings->service_charge === 1 && $amount) {
            if ($gatewaySettings->charge_type === 'P') {
                $charge = ($gatewaySettings->charge / 100) * $amount;
            } elseif ($gatewaySettings->charge_type === 'F') {
                $charge = $gatewaySettings->charge;
            }
        }

        return currency_format($amount + $charge);
    }
}

if (! function_exists('serviceCharge')) {
    function serviceCharge(string $gateway, $amount = null)
    {
        $charge = 0;

        $gatewaySettings = SmPaymentGatewaySetting::where('gateway_name', $gateway)
            ->where('school_id', auth()->user()->school_id)
            ->first();

        if ($gatewaySettings && $gatewaySettings->service_charge === 1 && $amount) {

            if ($gatewaySettings->charge_type === 'P') {
                $charge = ($gatewaySettings->charge / 100) * $amount;
            } elseif ($gatewaySettings->charge_type === 'F') {
                $charge = $gatewaySettings->charge;
            }
        }

        return currency_format($charge);
    }
}

if (! function_exists('addIncome')) {
    function addIncome($payment_method, $name, $amount, $fees_colection_id, $user_id, $request = null)
    {
        $payment_method = SmPaymentMethhod::where('method', $payment_method)->first();
        $income_head = generalSetting();

        $add_income = new SmAddIncome();
        $add_income->name = $name;
        $add_income->date = date('Y-m-d');
        $add_income->amount = $amount;
        $add_income->fees_collection_id = $fees_colection_id;
        $add_income->active_status = 1;
        $add_income->income_head_id = $income_head->income_head_id;
        $add_income->payment_method_id = $payment_method->id;
        $add_income->created_by = $user_id;
        $add_income->school_id = auth()->user()->school_id;
        if (moduleStatusCheck('University')) {
            $common = App::make(UnCommonRepositoryInterface::class);
            $common->storeUniversityData($add_income, $request);
            $add_income->un_academic_id = getAcademicId();
        } else {
            $add_income->academic_id = getAcademicId();
        }

        $add_income->academic_id = getAcademicId();

        return $add_income->save();

    }
}

if (! function_exists('carryForwardLog')) {
    function carryForwardLog($record_id, $amount, $amount_type, $note, $type): bool
    {
        $storeLog = new FeesCarryForwardLog();
        $storeLog->student_record_id = $record_id;
        $storeLog->amount = $amount;
        $storeLog->amount_type = $amount_type;
        $storeLog->date = date('Y-m-d H:i:s');
        $storeLog->note = $note;
        $storeLog->type = $type;
        $storeLog->created_by = auth()->user()->id;
        $storeLog->school_id = auth()->user()->school_id;
        $storeLog->save();

        return true;
    }
}

if (! function_exists('directFees')) {
    function directFees(): bool
    {
        return (bool) generalSetting()->direct_fees_assign;
    }
}

if (! function_exists('discountFees')) {
    function discountFees($installment_id)
    {
        $amount = 0;
        $installment = DirectFeesInstallmentAssign::find($installment_id);
        if ($installment) {
            return $installment->amount - $installment->discount_amount;
        }

        return $amount;
    }
}

if (! function_exists('discount_fees')) {
    function discount_fees($amount, $discount = 0)
    {
        if ($discount) {
            return $amount - $discount;
        }

        return $amount;

    }
}

if (! function_exists('feesCarryForward')) {
    function feesCarryForward($studentRecordId, $feesType, array $payableAmount, $sub_total): ?array
    {
        $carryForward = SmFeesCarryForward::where('student_id', $studentRecordId)->first();

        if (! $carryForward) {
            return null;
        }

        $settings = FeesCarryForwardSettings::first();

        if (Carbon::now()->format('Y-m-d') <= $carryForward->due_date) {

            $totalPayableAmount = 0;
            foreach ($payableAmount as $amount) {
                $totalPayableAmount += $amount;
            }

            if ($carryForward->balance_type === 'due' && $carryForward->balance > 0) {

                $dueBalance = $carryForward->balance;
                if ($carryForward) {
                    $fees_type = new FmFeesType();
                    $fees_type->type = 'fees_carry';
                    $fees_type->name = $carryForward->notes ?: $settings->title;
                    $fees_type->school_id = auth()->user()->school_id;
                    $fees_type->academic_id = getAcademicId();
                    $fees_type->save();
                }

                $data['feesTypes'] = array_merge($feesType, [(int) $fees_type->id]);
                $data['amount'] = array_merge($payableAmount, [(int) $dueBalance]);
                $data['sub_total'] = array_merge($sub_total, [(int) $dueBalance]);
                $data['type'] = 'due';
                $updateCarry = SmFeesCarryForward::where('student_id', $studentRecordId)->first();
                $updateCarry->balance = 0;
                $updateCarry->balance_type = 'add';
                $updateCarry->update();
                carryForwardLog($studentRecordId, $dueBalance, 'due', 'Fees Payment', 'fees');
            } elseif ($totalPayableAmount <= $carryForward->balance) {
                $addBalance = $carryForward->balance - $totalPayableAmount;
                $updateCarry = SmFeesCarryForward::where('student_id', $studentRecordId)->first();
                $updateCarry->balance = $addBalance;
                $updateCarry->balance_type = 'add';
                $updateCarry->update();
                carryForwardLog($studentRecordId, $totalPayableAmount, 'due', 'Fees Payment Added', 'fees');
                carryForwardLog($studentRecordId, $addBalance, 'add', 'Fees Payment and Carry Ballance Added', 'fees');
                $data['paymentAmount'] = $payableAmount;
                $data['type'] = 'full_paid_add_xtra_amount';
            } else {

                $cAmount = $carryForward->balance;

                $paidFeesType = [];
                $paidFeesAmount = [];

                foreach ($feesType as $key => $type) {

                    $paidFeesType[$key] = $type;

                    if ($cAmount > 0) {

                        $pAmount = $payableAmount[$key] * 1;

                        if ($cAmount >= $pAmount) {
                            $paidFeesAmount[$key] = $pAmount;
                            $cAmount -= $pAmount;
                        } elseif ($cAmount < $pAmount) {
                            $paidFeesAmount[$key] = $cAmount;
                            $cAmount = 0;
                        } else {
                            $paidFeesAmount[$key] = 0;
                        }
                    }
                }

                $updateCarry = SmFeesCarryForward::where('student_id', $studentRecordId)->first();

                $updateCarry->balance = null;
                $updateCarry->balance_type = 'add';
                $updateCarry->update();

                carryForwardLog($studentRecordId, $cAmount, 'due', 'Fees Payment', 'fees');

                $data['paidFeesType'] = $paidFeesType;
                $data['paidFeesAmount'] = $paidFeesAmount;
                $data['type'] = 'multi_payment';
            }

            $data['paymentMethod'] = $settings->payment_gateway;

            return $data;
        }

        return null;

    }
}

if (! function_exists('feesInvoiceNumber')) {
    function feesInvoiceNumber($invoice): string
    {
        $settings = feesInvoiceSettings();
        $positions = json_decode($settings->invoice_positions);
        $format = '';
        foreach ($positions as $position) {
            if ($format  && $format !== '0') {
                $format .= '-';
            }

            $format .= $position->id;
        }

        $format .= '-inv_id';

        $key = [
            'prefix',
            'admission_no',
            'class',
            'section',
            'inv_id',
        ];

        $value = [
            $settings->prefix,
            Str::limit(@$invoice->studentInfo->admission_no, $settings->admission_limit),
            Str::limit(@$invoice->recordDetail->class->class_name, $settings->class_limit),
            Str::limit(@$invoice->recordDetail->section->section_name, $settings->section_limit),
            $settings->uniq_id_start + $invoice->id,
        ];

        return str_replace($key, $value, $format);
    }
}

if (! function_exists('feesInvoiceSettings')) {
    function feesInvoiceSettings()
    {
        return FmFeesInvoiceSettings::where('school_id', Auth::user()->school_id)->first();
    }
}

if (! function_exists('feesPayment')) {
    function feesPayment($type_id, $student_id)
    {
        try {
            return SmFeesPayment::where('active_status', 1)->where('fees_type_id', $type_id)->where('student_id', $student_id)->get();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('feesPaymentStatus')) {
    function feesPaymentStatus($installment_id): array
    {
        if (moduleStatusCheck('University')) {
            $feesInstallment = UnFeesInstallmentAssign::find($installment_id);
            $balance_fees = discountFeesAmount($feesInstallment->id) - ($feesInstallment->paid_amount);
            if ($feesInstallment->active_status === 1 && $balance_fees === 0) {
                $paid = __('fees.paid');

                return [$paid, 'bg-success'];
            }

            if ($feesInstallment->active_status === 2 || ($feesInstallment->paid_amount > 0)) {
                $partial = __('fees.partial');

                return [$partial, 'bg-warning'];
            }

            $unpaid = __('fees.unpaid');

            return [$unpaid, 'bg-danger'];

        }

        $feesInstallment = DirectFeesInstallmentAssign::find($installment_id);
        $balance_fees = discount_fees($feesInstallment->amount, $feesInstallment->discount_amount) - ($feesInstallment->paid_amount);
        if ($feesInstallment->active_status === 1 && $balance_fees === 0) {
            $paid = __('fees.paid');

            return [$paid, 'bg-success'];
        }

        if ($feesInstallment->active_status === 2 || ($feesInstallment->paid_amount > 0)) {
            $partial = __('fees.partial');

            return [$partial, 'bg-warning'];
        }

        $unpaid = __('fees.unpaid');

        return [$unpaid, 'bg-danger'];

    }
}

if (! function_exists('fees_payment_status')) {
    function fees_payment_status($amount, $discount = 0, $paid_amo = 0, $status = null): array
    {
        $balance_fees = ($amount - $discount) - ($paid_amo);
        if (moduleStatusCheck('University')) {
            if ($status === 1 && $balance_fees === 0) {
                $paid = __('fees.paid');

                return [$paid, 'bg-success'];
            }
            if ($status === 2 || ($paid_amo > 0)) {
                $partial = __('fees.partial');

                return [$partial, 'bg-warning'];
            }
            $unpaid = __('fees.unpaid');

            return [$unpaid, 'bg-danger'];
        }

        if ($status === 1 && $balance_fees === 0) {
            $paid = __('fees.paid');

            return [$paid, 'bg-success'];
        }

        if ($status === 2 || ($paid_amo > 0)) {
            $partial = __('fees.partial');

            return [$partial, 'bg-warning'];
        }

        $unpaid = __('fees.unpaid');

        return [$unpaid, 'bg-danger'];

    }
}

if (! function_exists('getValueByStringDuesFees')) {
    function getValueByStringDuesFees($student_detail, $str, array $fees_info)
    {

        if ($str === 'student_name') {

            return @$student_detail->full_name;
        }

        if ($str === 'parent_name') {

            $parent_info = SmParent::find($student_detail->parent_id);

            return @$parent_info->fathers_name;
        }

        if ($str === 'due_amount') {

            return @$fees_info['dues_fees'];
        }

        if ($str === 'due_date') {

            $fees_master = SmFeesMaster::find($fees_info['fees_master']);

            return @$fees_master->date;
        }

        if ($str === 'school_name') {

            return Auth::user()->school->school_name;
        }

        if ($str === 'fees_name') {

            $fees_master = SmFeesMaster::find($fees_info['fees_master']);

            return $fees_master->feesTypes->name;
        }

        return null;
    }
}

if (! function_exists('paymentMethodName')) {
    function paymentMethodName($payment_method_id): bool
    {
        $paymentMethodName = SmPaymentMethhod::where('id', $payment_method_id)
            ->where('school_id', Auth::user()->school_id)
            ->first('method')->method;

        return $paymentMethodName === 'Bank';

    }
}

if (! function_exists('smFeesInvoice')) {
    function smFeesInvoice($invoice): string
    {
        $settings = FeesInvoice::where('school_id', auth()->user()->school_id)->first();

        $number = (($settings->start_form + $invoice) - 1);
        $format = $settings->prefix.'-'.$number;

        $key = [
            'prefix',
            'start_form',
        ];

        $value = [
            $settings->prefix,
            $settings->start_form,
        ];

        return str_replace($key, $value, $format);
    }
}

if (! function_exists('smPaymentRemainder')) {
    function smPaymentRemainder($school_id = null): ?bool
    {
        $today = date('Y-m-d');

        if (! $school_id) {
            $school_id = auth()->user()->school_id;
        }

        $notificationData = DirectFeesReminder::where('school_id', $school_id)
            ->first();
        $notificationType = json_decode($notificationData->notification_types);

        $dueDate = Carbon::parse($today)->addDays($notificationData->due_date_before)->format('Y-m-d');

        $feesDues = DirectFeesInstallmentAssign::where('school_id', $school_id)
            ->where('active_status', '!=', 1)
            ->where('due_date', $dueDate)
            ->get();

        foreach ($feesDues as $feeDue) {
            if (in_array('system', $notificationType)) {
                $message = 'Fees Remainder';
                $user_id = @$feeDue->recordDetail->student->user_id;
                $role_id = @$feeDue->recordDetail->student->role_id;
                sendNotification($message, '', $user_id, $role_id);
            }

            if (in_array('email', $notificationType)) {
                $reciver_email = @$feeDue->recordDetail->student->email;
                $receiver_name = @$feeDue->recordDetail->student->full_name;
                $purpose = 'university_fees_remainder';

                $data['student_name'] = @$feeDue->recordDetail->student->full_name;
                $data['class'] = @$feeDue->recordDetail->class->class_name;
                $data['section'] = @$feeDue->recordDetail->section->section_name;
                $data['semester_label'] = @$feeDue->recordDetail->unSemesterLabel->name;
                $data['academic'] = @$feeDue->recordDetail->academic->name;
                $data['fees_type'] = @$feeDue->feesType->name;
                $data['amount'] = $feeDue->amount;
                $data['due_date'] = dateConvert($feeDue->due_date);
                send_mail($reciver_email, $receiver_name, $purpose, $data);
            }

            if (in_array('sms', $notificationType)) {
                $reciver_number = @$feeDue->recordDetail->student->mobile;
                $purpose = 'university_fees_remainder';
                $data['student_name'] = @$feeDue->recordDetail->student->full_name;
                $data['class'] = @$feeDue->recordDetail->class->class_name;
                $data['section'] = @$feeDue->recordDetail->section->section_name;
                $data['semester_label'] = @$feeDue->recordDetail->unSemesterLabel->name;
                $data['academic'] = @$feeDue->recordDetail->academic->name;
                $data['fees_type'] = @$feeDue->feesType->name;
                $data['amount'] = $feeDue->amount;
                $data['due_date'] = dateConvert($feeDue->due_date);
                send_sms($reciver_number, $purpose, $data);
            }

            return true;
        }

        return null;
    }
}

if (! function_exists('sm_fees_invoice')) {
    function sm_fees_invoice($invoice, $setting): string
    {
        $number = (($setting->start_form + $invoice) - 1);
        $format = $setting->prefix.'-'.$number;

        $key = [
            'prefix',
            'start_form',
        ];

        $value = [
            $setting->prefix,
            $setting->start_form,
        ];

        return str_replace($key, $value, $format);
    }
}

if (! function_exists('universityFeesInvoice')) {
    function universityFeesInvoice($invoice): string
    {
        $settings = FeesInvoice::where('school_id', auth()->user()->school_id)
            ->first();

        $number = $settings->start_form + $invoice;
        $format = $settings->prefix.'-'.$number;

        $key = [
            'prefix',
            'start_form',
        ];

        $value = [
            $settings->prefix,
            $settings->start_form,
        ];

        return str_replace($key, $value, $format);
    }
}
