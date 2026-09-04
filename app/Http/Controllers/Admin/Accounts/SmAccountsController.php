<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Accounts\SmFundTransferRequest;
use App\Http\Requests\Admin\Accounts\SmProfitLossRequest;
use App\Models\ApiBaseMethod;
use App\Models\SmAddExpense;
use App\Models\SmAddIncome;
use App\Models\SmAmountTransfer;
use App\Models\SmBankAccount;
use App\Models\SmBankStatement;
use App\Models\SmFeesMaster;
use App\Models\SmFeesPayment;
use App\Models\SmHrPayrollGenerate;
use App\Models\SmItemReceive;
use App\Models\SmItemSell;
use App\Models\SmPaymentMethhod;
use App\Traits\NotificationSend;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmAccountsController extends Controller
{
    use NotificationSend;

    public function searchAccount()
    {
        /*
        try {
        */
        return view('backEnd.accounts.search_income');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function searchAccountReportByDate(Request $request)
    {
        $request->validate([
            'type' => 'required',
        ]);
        /*
        try {
        */
        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));
        $date_time_from = date('Y-m-d H:i:s', strtotime($request->date_from));
        $date_time_to = date('Y-m-d H:i:s', strtotime($request->date_to.' '.'23:59:00'));
        $type_id = $request->type;
        $from_date = $request->date_from;
        $to_date = $request->date_to;
        if ($request->type === 'In') {
            if ($request->filtering_income === 'all') {
                $dormitory = 0;
                $transport = 0;
                $add_incomes = SmAddIncome::where('date', '>=', $date_from)
                    ->where('date', '<=', $date_to)
                    ->where('active_status', 1)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->get();

                $fees_payments = SmFeesPayment::where('updated_at', '>=', $date_time_from)
                    ->where('updated_at', '<=', $date_time_to)
                    ->where('active_status', 1)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('amount');

                $item_sells = SmItemSell::where('updated_at', '>=', $date_time_from)
                    ->where('updated_at', '<=', $date_time_to)
                    ->where('active_status', 1)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_paid');
            } elseif ($request->filtering_income === 'sell') {
                $dormitory = 0;
                $transport = 0;
                $add_incomes = [];
                $fees_payments = '';
                $item_sells = SmItemSell::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('total_paid');
            } elseif ($request->filtering_income === 'fees') {
                $dormitory = 0;
                $add_incomes = [];
                $transport = 0;
                $item_sells = '';
                $fees_payments = SmFeesPayment::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('amount');
            } elseif ($request->filtering_income === 'dormitory') {
                $add_incomes = [];
                $fees_payments = '';
                $item_sells = '';
                $transport = 0;
                $fees_masters = SmFeesMaster::select('fees_type_id')->Where('fees_group_id', 2)->where('school_id', Auth::user()->school_id)->get();
                $dormitory = 0;
                foreach ($fees_masters as $fee_master) {
                    $dormitory += SmFeesPayment::where('fees_type_id', $fee_master->fees_type_id)->where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('amount');
                }
            } else {
                $add_incomes = [];
                $fees_payments = '';
                $item_sells = '';
                $dormitory = 0;
                $fees_masters = SmFeesMaster::select('fees_type_id')->Where('fees_group_id', 1)->where('school_id', Auth::user()->school_id)->get();
                $transport = 0;
                foreach ($fees_masters as $fee_master) {
                    $transport += SmFeesPayment::where('fees_type_id', $fee_master->fees_type_id)->where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('amount');
                }
            }

            return view('backEnd.accounts.search_income', ['add_incomes' => $add_incomes, 'fees_payments' => $fees_payments, 'item_sells' => $item_sells, 'dormitory' => $dormitory, 'transport' => $transport, 'type_id' => $type_id, 'from_date' => $from_date, 'to_date' => $to_date]);
        }

        if ($request->filtering_expense === 'all') {
            $add_expenses = SmAddExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            $item_receives = SmItemReceive::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('total_paid');
            $payroll_payments = SmHrPayrollGenerate::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('payroll_status', 'P')->where('school_id', Auth::user()->school_id)->sum('net_salary');
        } elseif ($request->filtering_expense === 'receive') {
            $add_expenses = [];
            $item_receives = SmItemReceive::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('total_paid');
            $payroll_payments = '';
        } else {
            $add_expenses = [];
            $item_receives = '';
            $payroll_payments = SmItemReceive::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('total_paid');
        }

        return view('backEnd.accounts.search_income', ['add_expenses' => $add_expenses, 'item_receives' => $item_receives, 'payroll_payments' => $payroll_payments, 'type_id' => $type_id, 'from_date' => $from_date, 'to_date' => $to_date]);

        /*
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function searchExpense()
    {
        /*
        try {
        */
        return view('backEnd.accounts.search_expense');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function searchExpenseReportByDate(Request $request)
    {
        /*
        try {
        */
        date_default_timezone_set('Asia/Dhaka');
        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));
        $date_time_from = date('Y-m-d H:i:s', strtotime($request->date_from));
        $date_time_to = date('Y-m-d H:i:s', strtotime($request->date_to.' '.'23:59:00'));
        $add_expenses = SmAddExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->where('academic_id', getAcademicId())->get();
        $item_receives = SmItemReceive::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->sum('total_paid');
        $payroll_payments = SmHrPayrollGenerate::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('payroll_status', 'P')->where('school_id', Auth::user()->school_id)->sum('net_salary');

        return view('backEnd.accounts.search_expense', ['add_expenses' => $add_expenses, 'item_receives' => $item_receives, 'payroll_payments' => $payroll_payments]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function profit(Request $request)
    {
        /*
        try {
        */
        $user = Auth::user();
        $add_incomes = SmAddIncome::where('active_status', 1)
            ->where('name', '!=', 'Fund Transfer')
            ->where('school_id', $user->school_id)
            ->where('academic_id', getAcademicId())
            ->sum('amount');

        $total_income = $add_incomes;

        $add_expenses = SmAddExpense::where('active_status', 1)
            ->where('name', '!=', 'Fund Transfer')
            ->where('school_id', $user->school_id)
            ->where('academic_id', getAcademicId())
            ->sum('amount');

        $total_expense = $add_expenses;

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['total_income'] = $total_income;
            $data['total_expense'] = $total_expense;

            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.accounts.profit', ['total_income' => $total_income, 'total_expense' => $total_expense]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function searchProfitByDate(SmProfitLossRequest $smProfitLossRequest)
    {
        /*
        try {
        */
        date_default_timezone_set('Asia/Dhaka');

        $rangeArr = $smProfitLossRequest->date_range ? explode('-', $smProfitLossRequest->date_range) : ''.date('m/d/Y').' - '.date('m/d/Y').'';

        if ($smProfitLossRequest->date_range) {
            $date_from = DateTimeImmutable::createFromFormat('d/m/Y', trim($rangeArr[0]))
                ?: new DateTimeImmutable(trim($rangeArr[0]));
            $date_to = DateTimeImmutable::createFromFormat('d/m/Y', trim($rangeArr[1]))
                ?: new DateTimeImmutable(trim($rangeArr[1]));
            $date_from = Carbon::parse($date_from)->format('Y-m-d');
            $date_to = Carbon::parse($date_to)->format('Y-m-d');
        }

        $date_time_from = date('Y-m-d H:i:s', strtotime($rangeArr[0]));
        $date_time_to = date('Y-m-d H:i:s', strtotime($rangeArr[1].' '.'23:59:00'));
        $user = Auth::user();

        // Income
        $add_incomes = SmAddIncome::where('name', '!=', 'Fund Transfer')->where('date', '>=', $date_from)
            ->where('date', '<=', $date_to)
            ->where('active_status', 1)
            ->where('school_id', $user->school_id)
            ->where('academic_id', getAcademicId())
            ->sum('amount');

        $total_income = $add_incomes;

        // expense
        $add_expenses = SmAddExpense::where('date', '>=', $date_from)
            ->where('name', '!=', 'Fund Transfer')
            ->where('date', '<=', $date_to)
            ->where('active_status', 1)
            ->where('school_id', $user->school_id)
            ->where('academic_id', getAcademicId())
            ->sum('amount');

        $total_expense = $add_expenses;

        return view('backEnd.accounts.profit', ['total_income' => $total_income, 'total_expense' => $total_expense, 'date_time_from' => $date_time_from, 'date_time_to' => $date_time_to]);
        /*
        } catch (Exception $exception) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function transaction()
    {
        /*
        try {
        */
        $payment_methods = SmPaymentMethhod::where('school_id', Auth::user()->school_id)->select(['method', 'id', 'type'])->get();

        return view('backEnd.accounts.transaction', ['payment_methods' => $payment_methods]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function transactionSearch(Request $request)
    {
        /*
        try {
        */
        $user = Auth::user();
        $type = $request->type;
        $rangeArr = $request->date_range ? explode('-', $request->date_range) : ''.date('m/d/Y').' - '.date('m/d/Y').'';
        if ($request->date_range) {
            $parseDate = static function (string $date): Carbon {
                $date = trim($date);

                foreach (['m/d/Y', 'd/m/Y', 'Y-m-d'] as $format) {
                    try {
                        return Carbon::createFromFormat($format, $date);
                    } catch (\Throwable) {
                        // Try the next supported date format.
                    }
                }

                return Carbon::parse($date);
            };

            $date_from = $parseDate($rangeArr[0])->startOfDay();
            $date_to = $parseDate($rangeArr[1])->endOfDay();
        }

        $payment_methods = SmPaymentMethhod::where('school_id', $user->school_id)->select(['method', 'id', 'type'])->get();
        $payment_method = $request->payment_method;

        if ($request->payment_method !== 'all') {
            $method_id = SmPaymentMethhod::find($request->payment_method);
            $search_info['method_id'] = $method_id?->id;
        }

        if ($request->date_range && $request->type === 'all' && $request->payment_method === 'all') {
            $add_incomes = SmAddIncome::where('date', '>=', $date_from)
                ->where('date', '<=', $date_to)
                ->where('active_status', 1)
                ->where('school_id', $user->school_id)
                ->where('academic_id', getAcademicId())
                ->select(['amount', 'payment_method_id', 'id'])
                ->with(['ACHead' => function ($query): void {
                    $query->select(['head', 'id']);
                }])
                ->get();
            $add_expenses = SmAddExpense::where('date', '>=', $date_from)
                ->where('date', '<=', $date_to)
                ->where('active_status', 1)
                ->where('school_id', $user->school_id)
                ->where('academic_id', getAcademicId())
                ->select(['name', 'payment_method_id', 'id', 'amount'])
                ->with(['paymentMethod' => function ($query): void {
                    $query->select(['method', 'id']);
                }])
                ->get();

            return view('backEnd.accounts.transaction', ['payment_methods' => $payment_methods, 'add_incomes' => $add_incomes, 'add_expenses' => $add_expenses, 'type' => $type]);
        }

        if ($request->date_range && $request->type === 'In') {
            if ($request->payment_method === 1 || $request->payment_method === 2 || $request->payment_method === 3 || $request->payment_method === 4 || $request->payment_method === 5) {
                $add_incomes = SmAddIncome::addIncome($date_from, $date_to, $payment_method)->get();

                return view('backEnd.accounts.transaction', ['payment_methods' => $payment_methods, 'add_incomes' => $add_incomes, 'search_info' => $search_info, 'type' => $type]);
            }

            $add_incomes = SmAddIncome::where('date', '>=', $date_from)
                ->where('date', '<=', $date_to)
                ->where('active_status', 1)
                ->where('school_id', $user->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            return view('backEnd.accounts.transaction', ['payment_methods' => $payment_methods, 'add_incomes' => $add_incomes, 'type' => $type]);
        }

        if ($request->date_range && $request->type === 'Ex') {
            if ($request->payment_method === 1 || $request->payment_method === 2 || $request->payment_method === 3 || $request->payment_method === 4 || $request->payment_method === 5) {
                $add_expenses = SmAddExpense::addExpense($date_from, $date_to, $payment_method)->get();

                return view('backEnd.accounts.transaction', ['payment_methods' => $payment_methods, 'add_expenses' => $add_expenses, 'search_info' => $search_info, 'type' => $type]);
            }

            $add_expenses = SmAddExpense::where('date', '>=', $date_from)
                ->where('date', '<=', $date_to)
                ->where('active_status', 1)
                ->where('school_id', $user->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            return view('backEnd.accounts.transaction', ['payment_methods' => $payment_methods, 'add_expenses' => $add_expenses, 'type' => $type]);
        }
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function accountsPayrollReport(Request $request)
    {
        /*
        try {
        */
        return view('backEnd.accounts.accounts_payroll_report');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function accountsPayrollReportSearch(Request $request)
    {
        /*
        try {
        */
        $rangeArr = $request->date_range ? explode('-', $request->date_range) : [date('m/d/Y'), date('m/d/Y')];
        $date_from = DateTimeImmutable::createFromFormat('d/m/Y', trim($rangeArr[0]))
            ?: new DateTimeImmutable(trim($rangeArr[0]));
        $date_to = DateTimeImmutable::createFromFormat('d/m/Y', trim($rangeArr[1]))
            ?: new DateTimeImmutable(trim($rangeArr[1]));

        $payroll_infos = SmAddExpense::where('date', '>=', $date_from)
            ->where('date', '<=', $date_to)
            ->where('active_status', 1)
            ->where('name', 'Staff Payroll')
            ->where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->when(moduleStatusCheck('Branch'), function ($query): void {
                $query->whereHas('ACHead', function ($query): void {
                    branchWiseApplyFilter($query, 'branch_id', userBranch());
                });
            })
            ->select(['amount', 'description', 'amount', 'id'])
            ->with(['paymentMethod' => function ($query): void {
                $query->select(['method', 'id', 'type']);
            },
                'account' => function ($query): void {
                    $query->select(['bank_name', 'id']);
                },
                'ACHead' => function ($query): void {
                    $query->select(['id', 'head', 'branch_id']);
                },
                'ACHead.branch' => function ($query): void {
                    $query->select(['id', 'branch_name']);
                },
            ])->select(['id', 'amount', 'description', 'payment_method_id', 'account_id', 'expense_head_id'])->get();

        return view('backEnd.accounts.accounts_payroll_report', ['payroll_infos' => $payroll_infos]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function fundTransfer()
    {
        /*
        try {
        */
        $user = Auth::user();
        $payment_methods = SmPaymentMethhod::get(['method', 'id']);
        $bank_accounts = SmBankAccount::where('school_id', $user->school_id)->get();
        $transfers = SmAmountTransfer::where('school_id', $user->school_id)
            ->select(['purpose', 'amount', 'id'])
            ->with(['toPaymentMethodName' => function ($query): void {
                $query->select(['method', 'id']);
            },
                'fromPaymentMethodName' => function ($query): void {
                    $query->select(['method', 'id']);
                },
            ])
            ->select(['id', 'purpose', 'amount', 'to_payment_method', 'from_payment_method'])
            ->get();
        $bank_amount = SmBankAccount::where('school_id', $user->school_id)->sum('current_balance');

        return view('backEnd.accounts.fund_transfer', ['payment_methods' => $payment_methods, 'bank_accounts' => $bank_accounts, 'transfers' => $transfers, 'bank_amount' => $bank_amount]);
        /* } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        } */
    }

    public function fundTransferStore(SmFundTransferRequest $smFundTransferRequest)
    {

        /*
        try {
        */
        // Validation Part Start
        $user = Auth::user();
        $fromPaymentMethod = (int) $smFundTransferRequest->from_payment_method;
        $toPaymentMethod = (int) $smFundTransferRequest->to_payment_method;
        $fromBankId = $smFundTransferRequest->from_bank_name ? (int) $smFundTransferRequest->from_bank_name : null;
        $toBankId = $smFundTransferRequest->to_bank_name ? (int) $smFundTransferRequest->to_bank_name : null;

        if ($fromPaymentMethod === 3 && ! $fromBankId) {
            Toastr::error('Bank Name is Required', 'Error');

            return redirect()->back();
        }

        if ($toPaymentMethod === 3 && ! $toBankId) {
            Toastr::error('Bank Name is Required', 'Error');

            return redirect()->back();
        }

        if ($fromPaymentMethod === 3 && $toPaymentMethod === 3 && $fromBankId === $toBankId) {
            $message = SmBankAccount::where('id', $fromBankId)
                ->where('school_id', $user->school_id)
                ->first();

            Toastr::warning(@$message->bank_name.' to '.@$message->bank_name.' transfer is not accepted', 'Warning');

            return redirect()->back();
        }

        if ($fromPaymentMethod === $toPaymentMethod && $fromPaymentMethod !== 3) {
            $message = SmPaymentMethhod::where('id', $fromPaymentMethod)
                ->where('school_id', $user->school_id)
                ->first();
            Toastr::warning(@$message->method.' to '.@$message->method.' transfer is not accepted', 'Warning');

            return redirect()->back();
        }

        // Validation Part End

        $from_payment = SmPaymentMethhod::where('school_id', $user->school_id)->findOrFail($smFundTransferRequest->from_payment_method);

        if ($from_payment->method === 'Bank') {
            $balance = SmBankAccount::where('school_id', $user->school_id)->findOrFail($smFundTransferRequest->from_bank_name)->current_balance;

            if ($balance >= $smFundTransferRequest->amount && $balance !== 0) {
                $transfer = new SmAmountTransfer();
                $transfer->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
                $transfer->purpose = $smFundTransferRequest->purpose;
                $transfer->from_payment_method = $smFundTransferRequest->from_payment_method;
                $transfer->from_bank_name = $smFundTransferRequest->from_bank_name;
                $transfer->to_payment_method = $smFundTransferRequest->to_payment_method;
                $transfer->to_bank_name = $smFundTransferRequest->to_bank_name;
                $transfer->transfer_date = Carbon::now();
                $transfer->school_id = $user->school_id;
                $transfer->academic_id = getAcademicId();
                $transfer->save();

                $add_expense = new SmAddExpense();
                $add_expense->name = 'Fund Transfer';
                $add_expense->date = Carbon::now();
                $add_expense->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
                $add_expense->payment_method_id = $smFundTransferRequest->from_payment_method;
                $add_expense->account_id = $smFundTransferRequest->from_bank_name;
                $add_expense->school_id = $user->school_id;
                $add_expense->academic_id = getAcademicId();
                $add_expense->save();

                $add_income = new SmAddIncome();
                $add_income->name = 'Fund Transfer';
                $add_income->date = Carbon::now();
                $add_income->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
                $add_income->payment_method_id = $smFundTransferRequest->to_payment_method;
                if ($smFundTransferRequest->to_bank_name) {
                    $add_income->account_id = $smFundTransferRequest->to_bank_name;
                }

                $add_income->account_id = $smFundTransferRequest->to_bank_name;
                $add_income->school_id = $user->school_id;
                $add_income->academic_id = getAcademicId();
                $add_income->save();

                $bank_id = SmBankAccount::where('id', $smFundTransferRequest->from_bank_name)
                    ->where('school_id', $user->school_id)
                    ->first();
                $bank_expense = $bank_id->current_balance - (float) round($smFundTransferRequest->amount, getDecimalDigit());

                $bank_statement = new SmBankStatement();
                $bank_statement->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
                $bank_statement->after_balance = $bank_expense;
                $bank_statement->type = 0;
                $bank_statement->details = 'Fund Transfer';
                $bank_statement->item_receive_id = $transfer->id;
                $bank_statement->payment_date = Carbon::now();
                $bank_statement->bank_id = $smFundTransferRequest->from_bank_name;
                $bank_statement->school_id = $user->school_id;
                $bank_statement->payment_method = $smFundTransferRequest->from_payment_method;
                $bank_statement->save();

                $new_balance = SmBankAccount::where('school_id', $user->school_id)->findOrFail($smFundTransferRequest->from_bank_name);
                $new_balance->current_balance = $bank_expense;
                $new_balance->update();

                if ($smFundTransferRequest->to_bank_name) {
                    $bank_id = SmBankAccount::where('id', $smFundTransferRequest->to_bank_name)
                        ->where('school_id', $user->school_id)
                        ->firstOrFail();
                    $bank_income = $bank_id->current_balance + (float) round($smFundTransferRequest->amount, getDecimalDigit());

                    $bank_statement = new SmBankStatement();
                    $bank_statement->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
                    $bank_statement->after_balance = $bank_income;
                    $bank_statement->type = 1;
                    $bank_statement->details = 'Fund Transfer';
                    $bank_statement->item_receive_id = $transfer->id;
                    $bank_statement->payment_date = Carbon::now();
                    $bank_statement->bank_id = $smFundTransferRequest->to_bank_name;
                    $bank_statement->school_id = $user->school_id;
                    $bank_statement->payment_method = $smFundTransferRequest->to_payment_method;
                    $bank_statement->save();

                    $new_balance = SmBankAccount::where('school_id', $user->school_id)->findOrFail($smFundTransferRequest->to_bank_name);
                    $new_balance->current_balance = $bank_income;
                    $new_balance->update();
                }

                $data['amount'] = $transfer->amount;
                $this->sent_notifications('Fund_Transfer', [auth()->user()->id], $data, ['Super admin']);

                Toastr::success('Operation successful', 'Success');

                return redirect('fund-transfer');
            }

            Toastr::error('Operation Failed1', 'Failed');

            return redirect()->back();

        }

        $income = SmAddIncome::where('payment_method_id', $fromPaymentMethod)
            ->where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->where('name', '!=', 'Fund Transfer')
            ->sum('amount');

        $expense = SmAddExpense::where('payment_method_id', $fromPaymentMethod)
            ->where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->where('name', '!=', 'Fund Transfer')
            ->sum('amount');

        $balance = (float) $income - (float) $expense;
        $amount = (float) $smFundTransferRequest->amount;

        if ($balance < $amount) {
            Toastr::error("Insufficient balance. Available: ".number_format($balance, 2).", Required: ".number_format($amount, 2), 'Failed');

            return redirect()->back();
        }

        $transfer = new SmAmountTransfer();
        $transfer->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
        $transfer->purpose = $smFundTransferRequest->purpose;
        $transfer->from_payment_method = $smFundTransferRequest->from_payment_method;
        $transfer->to_payment_method = $smFundTransferRequest->to_payment_method;
        if ($smFundTransferRequest->to_bank_name) {
            $transfer->to_bank_name = $smFundTransferRequest->to_bank_name;
        }

        $transfer->transfer_date = Carbon::now();
        $transfer->school_id = $user->school_id;
        $transfer->academic_id = getAcademicId();
        $transfer->save();

        $add_expense = new SmAddExpense();
        $add_expense->name = 'Fund Transfer';
        $add_expense->date = Carbon::now();
        $add_expense->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
        $add_expense->payment_method_id = $smFundTransferRequest->from_payment_method;
        if ($smFundTransferRequest->to_bank_name) {
            $add_expense->account_id = $smFundTransferRequest->to_bank_name;
        }

        $add_expense->school_id = $user->school_id;
        $add_expense->academic_id = getAcademicId();
        $add_expense->save();

        $add_income = new SmAddIncome();
        $add_income->name = 'Fund Transfer';
        $add_income->date = Carbon::now();
        $add_income->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
        $add_income->payment_method_id = $smFundTransferRequest->to_payment_method;
        if ($smFundTransferRequest->to_bank_name) {
            $add_expense->account_id = $smFundTransferRequest->to_bank_name;
        }

        $add_income->school_id = $user->school_id;
        $add_income->academic_id = getAcademicId();
        $add_income->save();

        if ($smFundTransferRequest->to_bank_name) {

            $bank_id = SmBankAccount::where('id', $smFundTransferRequest->to_bank_name)
                ->where('school_id', $user->school_id)
                ->first();

            $bank_income = $bank_id->current_balance + (float) round($smFundTransferRequest->amount, getDecimalDigit());

            $bank_statement = new SmBankStatement();
            $bank_statement->amount = (float) round($smFundTransferRequest->amount, getDecimalDigit());
            $bank_statement->after_balance = $bank_income;
            $bank_statement->type = 1;
            $bank_statement->details = 'Fund Transfer';
            $bank_statement->item_receive_id = $transfer->id;
            $bank_statement->payment_date = Carbon::now();
            $bank_statement->bank_id = $smFundTransferRequest->to_bank_name;
            $bank_statement->school_id = $user->school_id;
            $bank_statement->payment_method = $smFundTransferRequest->to_payment_method;
            $bank_statement->save();

            $new_balance = SmBankAccount::find($smFundTransferRequest->to_bank_name);
            $new_balance->current_balance = $bank_income;
            $new_balance->update();
        }
        $data['amount'] = $transfer->amount;
        $this->sent_notifications('Fund_Transfer', [auth()->user()->id], $data, ['Super admin']);
        Toastr::success('Operation successful', 'Success');

        return redirect('fund-transfer');
    }
    /*
    } catch (Exception $e) {
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();
    }
    */

}
