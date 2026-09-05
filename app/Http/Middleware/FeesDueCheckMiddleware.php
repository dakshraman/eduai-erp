<?php

namespace App\Http\Middleware;

use App\Models\DirectFeesInstallmentAssign;
use App\Models\SmFeesAssign;
use App\Support\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Fees\Entities\FmFeesInvoice;
use Symfony\Component\HttpFoundation\Response;

class FeesDueCheckMiddleware
{
    /**
     * Cache TTL for due-fees status: 15 minutes.
     *
     * OPTIMIZED:
     * 1. Removed Cache::forget($cache_key) at the top — it was destroying the cache
     *    BEFORE checking it, so every request ran all queries even when cached.
     * 2. Now checks the cache FIRST and returns immediately on hit.
     * 3. Uses ModuleRegistry instead of moduleStatusCheck() for cheaper module checks.
     * 4. Extracted student/parent logic into focused private methods.
     */
    private const CACHE_TTL = 900; // 15 minutes

    public function handle(Request $request, Closure $next): Response
    {
        $settings = generalSetting();

        if (! $settings->due_fees_login) {
            return $next($request);
        }

        $user = auth()->user();

        if (! $user || $user->loginRestricted) {
            return $next($request);
        }

        $cacheKey = 'have_due_fees_'.$user->id;

        // Return immediately if cache is warm — no DB queries needed
        if (Cache::has($cacheKey)) {
            return $next($request);
        }

        $today = date('Y-m-d');

        if ($user->role_id === 2) {
            // Student
            $this->checkStudentDueFees($user, $today, $cacheKey, $settings);
        } elseif ($user->role_id === 3) {
            // Parent
            $this->checkParentDueFees($user, $today, $cacheKey, $settings);
        }

        return $next($request);
    }

    /**
     * Check due fees for a student user and cache the result.
     */
    private function checkStudentDueFees($user, string $today, string $cacheKey, $settings): void
    {
        $dueDate = null;

        if (ModuleRegistry::isActive('Fees') && $settings->fees_status) {
            $invoice = FmFeesInvoice::where('academic_id', getAcademicId())
                ->where('payment_status', 'not')
                ->where('student_id', $user->student->id)
                ->orderByDesc('due_date')
                ->first('due_date');
            $dueDate = $invoice?->due_date;

        } elseif (! $settings->fees_status && directFees()) {
            $installment = DirectFeesInstallmentAssign::where('active_status', '!=', 1)
                ->where('academic_id', getAcademicId())
                ->where('student_id', $user->student->id)
                ->orderByDesc('due_date')
                ->first('due_date');
            $dueDate = $installment?->due_date;

        } elseif (! $settings->fees_status) {
            $dueDate = $this->getClassicFeesDueDate($user->student->id);
        }

        if ($dueDate && $today > $dueDate) {
            Cache::put($cacheKey, true, self::CACHE_TTL);
        } else {
            // Cache a "no dues" result so we don't re-query next request either
            Cache::put($cacheKey, false, self::CACHE_TTL);
        }
    }

    /**
     * Check due fees for a parent user (checks all children) and cache the result.
     */
    private function checkParentDueFees($user, string $today, string $cacheKey, $settings): void
    {
        $studentIdsWithDues = [];
        $studentIds = $user->parent->childrens->pluck('id');

        foreach ($studentIds as $studentId) {
            if ($this->studentHasDueFees($studentId, $today, $settings)) {
                $studentIdsWithDues[] = $studentId;
            }
        }

        Cache::put($cacheKey, $studentIdsWithDues, self::CACHE_TTL);
    }

    /**
     * Check if a single student has overdue fees.
     */
    private function studentHasDueFees(int $studentId, string $today, $settings): bool
    {
        if (ModuleRegistry::isActive('Fees') && $settings->fees_status) {
            $invoice = FmFeesInvoice::where('academic_id', getAcademicId())
                ->where('payment_status', 'not')
                ->where('student_id', $studentId)
                ->orderByDesc('due_date')
                ->first('due_date');

            return $invoice && $today > $invoice->due_date;
        }

        if (! $settings->fees_status && directFees()) {
            $installment = DirectFeesInstallmentAssign::where('active_status', '!=', 1)
                ->where('academic_id', getAcademicId())
                ->where('student_id', $studentId)
                ->orderByDesc('due_date')
                ->first('due_date');

            return $installment && $today > $installment->due_date;
        }

        if (! $settings->fees_status) {
            $dueDate = $this->getClassicFeesDueDate($studentId);

            return $dueDate && $today > $dueDate;
        }

        return false;
    }

    /**
     * Calculate due date for classic (non-module) fees system.
     * Returns the latest overdue date, or null if no dues.
     */
    private function getClassicFeesDueDate(int $studentId): ?string
    {
        $allFees = SmFeesAssign::where('academic_id', getAcademicId())
            ->where('student_id', $studentId)
            ->whereHas('feesGroupMaster')
            ->with(['feesGroupMaster.feesTypes'])
            ->get();

        foreach ($allFees as $fee) {
            $paid = SmFeesAssign::feesPayment($fee->feesGroupMaster->feesTypes->id, $fee->student_id, $fee->record_id)->sum('amount');
            $fine = SmFeesAssign::feesPayment($fee->feesGroupMaster->feesTypes->id, $fee->student_id, $fee->record_id)->sum('fine');
            $balance = $fee->feesGroupMaster->amount - ($fee->applied_discount + $paid) + $fine;

            if ($balance > 0) {
                return $fee->feesGroupMaster->date;
            }
        }

        return null;
    }
}
