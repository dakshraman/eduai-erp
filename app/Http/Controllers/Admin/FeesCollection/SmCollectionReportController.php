<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\Http\Controllers\Controller;
use App\Models\ApiBaseMethod;
use App\Models\DireFeesInstallmentChildPayment;
use App\Models\SmClass;
use App\Models\SmFeesPayment;
use App\Models\StudentRecord;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\University\Entities\UnFeesInstallmentAssign;

class SmCollectionReportController extends Controller
{
    public function transactionReport(Request $request)
    {
        /*
        try {
        */
        $classes = branchWise(SmClass::get());
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse(null, null);
        }

        return view('backEnd.feesCollection.transaction_report', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function transactionReportSearch(Request $request)
    {
        $rangeArr = $request->date_range ? explode('-', $request->date_range) : ''.date('m/d/Y').' - '.date('m/d/Y').'';
        $date_from = null;
        $date_to = null;
        if ($request->date_range) {
            $date_from = new DateTimeImmutable(trim($rangeArr[0]));
            $date_to = new DateTimeImmutable(trim($rangeArr[1]));
        }

        $classes = [];
        /*
        try {
        */
        if (moduleStatusCheck('University')) {
            $StudentRecord = branchWise(StudentRecord::query());
            $students = universityFilter($StudentRecord, $request)->get();

            $fees_payments = UnFeesInstallmentAssign::with('payments')->whereIn('active_status', [1, 2])
                ->whereIn('student_id', $students->pluck('student_id'))
                ->where('un_semester_label_id', $request->un_semester_label_id)
                ->where('school_id', auth()->user()->school_id)
                ->when($request->date_range, function ($q) use ($date_from, $date_to): void {
                    $q->where('payment_date', '>=', $date_from);
                    $q->where('payment_date', '<=', $date_to);
                })
                ->where('paid_amount', '>', 0)
                ->get();
        } elseif (directFees()) {
            $classes = branchWise(SmClass::get());
            $allStudent = branchWise(StudentRecord::when($request->class, function ($q) use ($request): void {
                $q->where('class_id', $request->class);
            })
                ->when($request->section, function ($q) use ($request): void {
                    $q->where('section_id', $request->section);
                })
                ->when($request->shift, function ($q) use ($request): void {
                    $q->where('shift_id', $request->shift);
                })
                ->when($request->branch_id, function ($q) use ($request): void {
                    $q->where('branch_id', $request->branch_id);
                })
                ->where('academic_id', getAcademicId())
                ->get());
            $fees_payments = DireFeesInstallmentChildPayment::with('installmentAssign.recordDetail.studentDetail', 'installmentAssign.installment')->where('active_status', 1)
                ->whereIn('record_id', $allStudent->pluck('id'))
                ->when($request->date_range, function ($q) use ($date_from, $date_to): void {
                    $q->where('payment_date', '>=', $date_from);
                    $q->where('payment_date', '<=', $date_to);
                })
                ->where('paid_amount', '>', 0)
                ->where('school_id', auth()->user()->school_id)
                ->get();
        } else {
            $classes = branchWise(SmClass::get());
            if ($request->date_range) {
                if ($request->class) {
                    $students = branchWise(StudentRecord::where('class_id', $request->class)
                        ->get());

                    $fees_payments = branchWise(SmFeesPayment::where('active_status', 1)
                        ->whereIn('student_id', $students->pluck('student_id'))
                        ->where('payment_date', '>=', $date_from)
                        ->where('payment_date', '<=', $date_to)
                        ->when($request->branch_id, function ($q) use ($request): void {
                            $q->where('branch_id', $request->branch_id);
                        })
                        ->where('school_id', Auth::user()->school_id)
                        ->get());
                    $fees_payments = $fees_payments->groupBy('student_id');
                } else {
                    $fees_payments = branchWise(SmFeesPayment::where('active_status', 1)
                        ->where('payment_date', '>=', $date_from)
                        ->where('payment_date', '<=', $date_to)
                        ->when($request->branch_id, function ($q) use ($request): void {
                            $q->where('branch_id', $request->branch_id);
                        })
                        ->where('school_id', Auth::user()->school_id)
                        ->get());
                    $fees_payments = $fees_payments->groupBy('student_id');
                }
            }

            if ($request->class && $request->section) {
                $students = branchWise(StudentRecord::where('class_id', $request->class)
                    ->where('section_id', $request->section)
                    ->when($request->branch_id, function ($q) use ($request): void {
                        $q->where('branch_id', $request->branch_id);
                    })
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->get());

                $fees_payments = branchWise(SmFeesPayment::where('active_status', 1)
                    ->whereIn('student_id', $students->pluck('student_id'))
                    ->where('payment_date', '>=', $date_from)
                    ->where('payment_date', '<=', $date_to)
                    ->when($request->branch_id, function ($q) use ($request): void {
                        $q->where('branch_id', $request->branch_id);
                    })
                    ->where('school_id', Auth::user()->school_id)
                    ->get());
                $fees_payments = $fees_payments->groupBy('student_id');

            }

            if ($request->class && $request->section && $request->shift) {
                $students = branchWise(StudentRecord::where('class_id', $request->class)
                    ->where('section_id', $request->section)
                    ->where('shift_id', $request->shift)
                    ->when($request->branch_id, function ($q) use ($request): void {
                        $q->where('branch_id', $request->branch_id);
                    })
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->get());

                $fees_payments = branchWise(SmFeesPayment::where('active_status', 1)
                    ->whereIn('student_id', $students->pluck('student_id'))
                    ->where('payment_date', '>=', $date_from)
                    ->where('payment_date', '<=', $date_to)
                    ->when($request->branch_id, function ($q) use ($request): void {
                        $q->where('branch_id', $request->branch_id);
                    })
                    ->where('school_id', Auth::user()->school_id)
                    ->get());
                $fees_payments = $fees_payments->groupBy('student_id');

            }

        }

        $class_id = $request->class;
        $section_id = $request->section;
        $shift_id = $request->shift ?? null;
        $branch_id = $request->branch_id ?? null;

        if (moduleStatusCheck('University')) {
            // $data = $this->unCommonRepository->oldValueSelected($request);
            return view('backEnd.feesCollection.transaction_report', ['fees_payments' => $fees_payments, 'date_to' => $date_to, 'date_from' => $date_from, 'branch_id' => $branch_id]);
        }

        if (directFees()) {
            // $data = $this->unCommonRepository->oldValueSelected($request);
            return view('backEnd.feesCollection.transaction_report', ['fees_payments' => $fees_payments, 'date_to' => $date_to, 'date_from' => $date_from, 'classes' => $classes, 'class_id' => $class_id, 'section_id' => $section_id, 'shift_id' => $shift_id, 'branch_id' => $branch_id]);
        }

        return view('backEnd.feesCollection.transaction_report', ['fees_payments' => $fees_payments, 'classes' => $classes, 'date_to' => $date_to, 'date_from' => $date_from, 'class_id' => $class_id, 'section_id' => $section_id, 'shift_id' => $shift_id, 'branch_id' => $branch_id]);

        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }
}
