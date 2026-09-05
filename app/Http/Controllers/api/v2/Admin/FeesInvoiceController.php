<?php

namespace App\Http\Controllers\api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmAcademicYear;
use App\Scopes\AcademicSchoolScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesInvoice;

class FeesInvoiceController extends Controller
{
    public function fees_invoice_index(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:sm_classes,id',
            'section_id' => 'nullable|exists:sm_sections,id',
        ]);
        $data['studentInvoices'] = FmFeesInvoice::withoutGlobalScope(AcademicSchoolScope::class)->where('type', 'fees')
            ->with('studentInfo', 'recordDetail')
            ->select('fm_fees_invoices.*')
            ->where('school_id', Auth::user()->school_id)
            ->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())
            ->when($request->name, function ($q) use ($request): void {
                $q->whereHas('studentInfo', function ($q) use ($request) {
                    return $q->where(function ($q) use ($request) {
                        return $q->where('first_name', 'like', '%'.$request->name.'%')
                            ->orWhere('last_name', 'like', '%'.$request->name.'%')
                            ->orWhere('full_name', 'like', '%'.$request->name.'%');
                    });
                });
            })
            ->when($request->class_id, function ($q2) use ($request): void {
                $q2->whereHas('recordDetail', function ($q2) use ($request) {
                    return $q2->where(function ($q2) use ($request) {
                        return $q2->where('class_id', $request->class_id);
                    });
                });
            })
            ->when($request->section_id, function ($q3) use ($request): void {
                $q3->whereHas('recordDetail', function ($q3) use ($request): void {
                    $q3->where(function ($q3) use ($request) {
                        return $q3->where('section_id', $request->section_id);
                    });
                });
            })
            ->latest('create_date')->get()->map(function ($value): array {
                $balance = $value->Tamount + $value->Tfine - ($value->Tpaidamount + $value->Tweaver);
                $paid_amount = $value->Tpaidamount;
                if ($balance === 0) {
                    $status = __('fees.paid');
                } elseif ($paid_amount > 0) {
                    $status = __('fees.partial');
                } else {
                    $status = __('fees.unpaid');
                }

                return [
                    'id' => (int) $value->id,
                    'full_name' => @$value->studentInfo->first_name.' '.@$value->studentInfo->last_name,
                    'class' => (string) @$value->recordDetail->class->class_name,
                    'section' => (string) @$value->recordDetail->section->section_name,
                    'date' => (string) dateConvert($value->create_date),
                    'amount' => (string) currency_format($value->Tamount),
                    'paid' => (string) currency_format($paid_amount),
                    'balance' => (string) currency_format($balance),
                    'status' => (string) $status,
                ];
            });


        return response()->json( [
            'success' => true,
            'data' => $data,
            'message' => 'Fees invoice list',
        ]);
    }
}
