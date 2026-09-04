<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeesCollection\SmFeesForwardSearchRequest;
use App\Http\Requests\FeesCarryForwardSettingsStoreRequest;
use App\Models\FeesCarryForwardLog;
use App\Models\FeesCarryForwardSettings;
use App\Models\SmAcademicYear;
use App\Models\SmClass;
use App\Models\SmFeesCarryForward;
use App\Models\SmPaymentMethhod;
use App\Models\SmStudent;
use App\Models\StudentRecord;
use App\Scopes\AcademicSchoolScope;
use App\Scopes\ActiveStatusSchoolScope;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Fees\Entities\FmFeesInvoice;
use Modules\Fees\Entities\FmFeesInvoiceChield;
use Yajra\DataTables\DataTables;

class SmFeesCarryForwardController extends Controller
{
    public function feesForward(Request $request)
    {
        /*
        try {
        */
        $classes = branchWise(SmClass::get());

        return view('backEnd.feesCollection.fees_forward', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function universityFeesForwardSearch($request)
    {
        $request->all();
        /*
        try {
        */
        $students = branchWise(StudentRecord::where('un_semester_label_id', $request->un_semester_label_id)
            ->where('un_section_id', $request->un_section_id)
            ->where('school_id', Auth::user()->school_id)
            ->whereHas('student', function ($q): void {
                $q->where('active_status', 1);
            })
            ->get());

        $un_session_id = $request->un_session_id;
        $un_faculty_id = $request->un_faculty_id;
        $un_department_id = $request->un_department_id;
        $un_academic_id = $request->un_academic_id;
        $un_semester_id = $request->un_semester_id;
        $un_semester_label_id = $request->un_semester_label_id;
        $un_section_id = $request->un_section_id;
        $branch_id = $request->branch_id ?? null;

        if ($students->count() !== 0) {
            foreach ($students as $student) {
                $fees_balance = SmFeesCarryForward::where('student_id', $student->student_id)->count();
            }
            if ($fees_balance === 0) {
                return view('backEnd.feesCollection.fees_forward', ['students' => $students, 'un_session_id' => $un_session_id, 'un_faculty_id' => $un_faculty_id, 'un_department_id' => $un_department_id, 'un_academic_id' => $un_academic_id, 'un_semester_id' => $un_semester_id, 'un_semester_label_id' => $un_semester_label_id, 'un_section_id' => $un_section_id, 'branch_id' => $branch_id]);
            }
            $update = '';

            return view('backEnd.feesCollection.fees_forward', ['students' => $students, 'update' => $update, 'un_session_id' => $un_session_id, 'un_faculty_id' => $un_faculty_id, 'un_department_id' => $un_department_id, 'un_academic_id' => $un_academic_id, 'un_semester_id' => $un_semester_id, 'un_semester_label_id' => $un_semester_label_id, 'un_section_id' => $un_section_id, 'branch_id' => $branch_id]);

        }

        Toastr::error('Operation Failed', 'Failed');

        return redirect('fees-forward');

        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function feesForwardSearch(SmFeesForwardSearchRequest $smFeesForwardSearchRequest)
    {
        if (moduleStatusCheck(('University'))) {
            return $this->universityFeesForwardSearch($smFeesForwardSearchRequest);
        }

        $input = $smFeesForwardSearchRequest->all();
        $validator = Validator::make($input, [
            'class' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        /*
        try {
        */
        $classes = branchWise(SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get());

        $studentQuery = StudentRecord::with('studentDetail.forwardBalance')
            ->where('class_id', $smFeesForwardSearchRequest->class)
            ->where('section_id', $smFeesForwardSearchRequest->section)
            ->when(shiftEnable() && ! empty($smFeesForwardSearchRequest->shift), function ($query) use ($smFeesForwardSearchRequest) {
                $query->where('shift_id', $smFeesForwardSearchRequest->shift);
            })
            ->where('school_id', Auth::user()->school_id)
            ->whereHas('student', function ($q): void {
                $q->where('active_status', 1);
            })->where('academic_id', getAcademicId())
            ->where('is_promote', 0);

        if (moduleStatusCheck('Branch') && $smFeesForwardSearchRequest->branch_id) {
            $studentQuery = branchWiseApplyFilter($studentQuery, 'branch_id', $smFeesForwardSearchRequest->branch_id);
        }

        $students = $studentQuery->get();

        if ($students->count() > 0) {
            $studentIds = $students->pluck('student_id')->unique();
            $existingBalances = SmFeesCarryForward::whereIn('student_id', $studentIds)
                ->where('school_id', Auth::user()->school_id)
                ->get()
                ->keyBy('student_id');
            $update = $existingBalances->isNotEmpty();

            foreach ($students as $student) {

                if (! $existingBalances->has($student->student_id)) {
                    $previous_academic = SmAcademicYear::where('id', '!=', getAcademicId())->orderBy('id', 'DESC')->first();
                    $feesQuery = FmFeesInvoice::withoutGlobalScope(AcademicSchoolScope::class)
                        ->with(['studentInfo', 'recordDetail'])->where('school_id', auth()->user()->school_id)
                        ->where('academic_id', $previous_academic?->id)
                        ->where('student_id', $student->student_id);

                    if (moduleStatusCheck('Branch') && $smFeesForwardSearchRequest->branch_id) {
                        $feesQuery = branchWiseApplyFilter($feesQuery, 'branch_id', $smFeesForwardSearchRequest->branch_id);
                    }

                    if (moduleStatusCheck('Branch')) {
                        $fees = $feesQuery->select(['id', 'branch_id'])->get();
                    } else {
                        $fees = $feesQuery->select(['id'])->get();
                    }
                    $totalDue = 0;
                    foreach ($fees as $fee) {
                        $due = FmFeesInvoiceChield::where('fees_invoice_id', $fee->id)->sum('due_amount');
                        $totalDue += $due;
                    }
                    if ($totalDue > 0) {
                        $due_days = generalSetting()->carry_forword_due_day;
                        $due_date = date('Y-m-d', strtotime('+'.$due_days.' days'));
                        SmFeesCarryForward::updateOrCreate([
                            'student_id' => $student->student_id,
                            'school_id' => Auth::user()->school_id,
                        ], [
                            'balance' => $totalDue,
                            'notes' => 'Previous year due',
                            'academic_id' => getAcademicId(),
                            'due_date' => $due_date,
                            'balance_type' => 'due',
                            'branch_id' => moduleStatusCheck('Branch') ? ($smFeesForwardSearchRequest->branch_id ?? null) : null,
                        ]);
                        $update = true;
                    }
                }
            }
            $class_id = $smFeesForwardSearchRequest->class;
            $section_id = $smFeesForwardSearchRequest->section;
            $shift_id = shiftEnable() ? $smFeesForwardSearchRequest->shift : null;
            $branch_id = $smFeesForwardSearchRequest->branch_id ?? null;
            if (! $update) {
                return view('backEnd.feesCollection.fees_forward', ['classes' => $classes, 'students' => $students, 'shift_id' => $shift_id, 'section_id' => $section_id,  'class_id' => $class_id, 'branch_id' => $branch_id]);
            }

            return view('backEnd.feesCollection.fees_forward', ['classes' => $classes, 'students' => $students, 'shift_id' => $shift_id, 'section_id' => $section_id,  'update' => $update, 'class_id' => $class_id, 'branch_id' => $branch_id]);

        }

        $class_id = $smFeesForwardSearchRequest->class;
        $section_id = $smFeesForwardSearchRequest->section;
        $shift_id = shiftEnable() ? $smFeesForwardSearchRequest->shift : null;
        $branch_id = $smFeesForwardSearchRequest->branch_id ?? null;

        Toastr::error('No Student Found', 'Failed');

        return view('backEnd.feesCollection.fees_forward', ['classes' => $classes, 'shift_id' => $shift_id, 'section_id' => $section_id, 'class_id' => $class_id, 'branch_id' => $branch_id]);

        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */

    }

    public function feesForwardStore(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $studentTable = (new SmStudent())->getTable();

        $rules = [
            'id' => ['required', 'array', 'min:1'],
            'id.*' => [
                'required',
                'integer',
                Rule::exists($studentTable, 'id')->where(fn ($query) => $query
                    ->where('school_id', $schoolId)
                    ->where('active_status', 1)),
            ],
            'balance' => ['required', 'array'],
            'balance.*' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:255'],
        ];

        if (moduleStatusCheck('Branch')) {
            $rules['branch_id'] = ['nullable', 'integer'];
        }

        $validated = $request->validate($rules);

        $dueDays = (int) generalSetting()->carry_forword_due_day;
        $dueDate = now()->addDays($dueDays)->toDateString();

        try {
            DB::transaction(function () use ($validated, $request, $schoolId, $dueDate): void {
                foreach ($validated['id'] as $studentId) {
                    $signedBalance = (float) ($validated['balance'][$studentId] ?? 0);

                    $data = [
                        'balance' => round(abs($signedBalance), getDecimalDigit()),
                        'balance_type' => $signedBalance < 0 ? 'due' : 'add',
                        'notes' => $validated['notes'][$studentId] ?? 'Fees Carry Forward',
                        'academic_id' => getAcademicId(),
                        'due_date' => $dueDate,
                    ];

                    if (moduleStatusCheck('Branch') && $request->filled('branch_id')) {
                        $data['branch_id'] = $validated['branch_id'];
                    }

                    SmFeesCarryForward::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'school_id' => $schoolId,
                        ],
                        $data
                    );
                }
            });

            Toastr::success('Operation successful', 'Success');

            return redirect()->route('fees_forward');
        } catch (Exception $exception) {
            report($exception);
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back()->withInput();
        }
    }

    public function feesCarryForwardSettingsView()
    {
        /*
        try {
        */
        $settings = FeesCarryForwardSettings::first();
        $paymeny_gateway = SmPaymentMethhod::query();
        $paymeny_gateway = $paymeny_gateway->where('school_id', auth()->user()->school_id);
        $paymeny_gateway->where('method', '!=', 'Bank');
        if (moduleStatusCheck('XenditPayment') === false) {
            $paymeny_gateway->where('method', '!=', 'Xendit');
        }

        if (moduleStatusCheck('RazorPay') === false) {
            $paymeny_gateway->where('method', '!=', 'RazorPay');
        }

        if (moduleStatusCheck('Raudhahpay') === false) {
            $paymeny_gateway->where('method', '!=', 'Raudhahpay');
        }

        if (moduleStatusCheck('KhaltiPayment') === false) {
            $paymeny_gateway->where('method', '!=', 'Khalti');
        }

        if (moduleStatusCheck('MercadoPago') === false) {
            $paymeny_gateway->where('method', '!=', 'MercadoPago');
        }

        if (moduleStatusCheck('CcAveune') === false) {
            $paymeny_gateway->where('method', '!=', 'CcAveune');
        }

        $paymeny_gateway = $paymeny_gateway->withoutGlobalScope(ActiveStatusSchoolScope::class);
        $paymeny_gateway = $paymeny_gateway->get();

        return view('backEnd.systemSettings.feesCarryForwardSettingsView', ['settings' => $settings, 'paymeny_gateway' => $paymeny_gateway]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function feesCarryForwardSettings(FeesCarryForwardSettingsStoreRequest $feesCarryForwardSettingsStoreRequest)
    {
        /*
        try {
        */
        $settings = FeesCarryForwardSettings::first();
        if ($feesCarryForwardSettingsStoreRequest->title) {
            $settings->title = $feesCarryForwardSettingsStoreRequest->title;
        }

        if ($feesCarryForwardSettingsStoreRequest->fees_due_days) {
            $settings->fees_due_days = $feesCarryForwardSettingsStoreRequest->fees_due_days;
        }

        if ($feesCarryForwardSettingsStoreRequest->payment_gateway) {
            $settings->payment_gateway = $feesCarryForwardSettingsStoreRequest->payment_gateway;
        }

        $settings->save();

        Toastr::success('Operation successful', 'Success');
        if (generalSetting()->fees_status === 1) {
            return redirect()->route('fees-carry-forward-settings-view');
        }

        return redirect()->route('fees-carry-forward-settings-view-fees-collection');

        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function feesCarryForward()
    {
        /*
        try {
        */
        $classes = branchWise(SmClass::get());

        return view('backEnd.systemSettings.feesCarryForwardView', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function feesCarryForwardSearch(SmFeesForwardSearchRequest $smFeesForwardSearchRequest)
    {
        /*
        try {
        */
        $data['classes'] = branchWise(SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get());

        $data['students'] = branchWise(StudentRecord::with('studentDetail.forwardBalance')
            ->where('class_id', $smFeesForwardSearchRequest->class)
            ->where('section_id', $smFeesForwardSearchRequest->section)
            ->when($smFeesForwardSearchRequest->branch_id, function ($query) use ($smFeesForwardSearchRequest) {
                $query->where('branch_id', $smFeesForwardSearchRequest->branch_id);
            })
            ->where('school_id', Auth::user()->school_id)
            ->whereHas('student', function ($q): void {
                $q->where('active_status', 1);
            })
            ->get());

        $settings = FeesCarryForwardSettings::first();
        if (!$settings) {
            $settings = new FeesCarryForwardSettings();
            $settings->title = 'Fees Carry Forward';
        }
        $data['settings'] = $settings;

        if ($data['students']->count() !== 0) {
            foreach ($data['students'] as $student) {
                $fees_balance = SmFeesCarryForward::where('student_id', $student->student_id)->count();
            }

            $data['class_id'] = $smFeesForwardSearchRequest->class;

            return view('backEnd.systemSettings.feesCarryForwardView', $data);
        }

        Toastr::error('No Student Found', 'Failed');
        if (generalSetting()->fees_status === 1) {
            return redirect()->route('fees-carry-forward-view');
        }

        return redirect()->route('fees-carry-forward-view-fees-collection');
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function feesCarryForwardStore(Request $request)
    {
        /*
        try {
        */
        foreach (gv($request, 'studentFeesInfo') as $studentInfo) {
            $type = 'add';
            if (preg_match('/[+,-]/i', gv($studentInfo, 'balance'), $match)) {
                $data = $match[0];
                $type = $data === '+' ? 'add' : 'due';
            }

            $deleteData = SmFeesCarryForward::where('student_id', gv($studentInfo, 'student_id'))->first();
            $studentBaseAmount = SmFeesCarryForward::where('student_id', gv($studentInfo, 'student_id'))->sum('balance');
            if ($deleteData) {
                $transcationAmount = $studentBaseAmount - abs(gv($studentInfo, 'balance', 0));
            } else {
                $transcationAmount = abs(gv($studentInfo, 'balance', 0));
            }

            if ($deleteData) {
                $deleteData->delete();
            }

            $fees_forward = new SmFeesCarryForward();
            $fees_forward->student_id = gv($studentInfo, 'student_id');
            $fees_forward->balance = abs(gv($studentInfo, 'balance', 0));
            $fees_forward->balance_type = $type;
            $fees_forward->due_date = date('Y-m-d', strtotime(gv($studentInfo, 'due_date')));
            $fees_forward->notes = gv($studentInfo, 'notes');
            $fees_forward->school_id = auth()->user()->school_id;
            $fees_forward->academic_id = getAcademicId();
            $fees_forward->branch_id = gv($studentInfo, 'branch_id') ?? null;
            $fees_forward->save();

            // Carry Forward Log Start
            $storeLog = new FeesCarryForwardLog();
            $storeLog->student_record_id = gv($studentInfo, 'student_id');
            $storeLog->amount = $transcationAmount;
            $storeLog->amount_type = $type;
            $storeLog->branch_id = gv($studentInfo, 'branch_id') ?? null;
            if (generalSetting()->fees_status === 1) {
                $storeLog->type = 'fees';
            } else {
                $storeLog->type = 'installment';
            }

            $storeLog->date = date('Y-m-d H:i:s');
            $storeLog->note = gv($studentInfo, 'notes');
            $storeLog->created_by = auth()->user()->id;
            $storeLog->school_id = auth()->user()->school_id;
            $storeLog->save();
            // Carry Forward Log End
        }

        Toastr::success('Operation successful', 'Success');
        if (generalSetting()->fees_status === 1) {
            return redirect()->route('fees-carry-forward-view');
        }

        return redirect()->route('fees-carry-forward-view-fees-collection');

        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function feesCarryForwardLogView()
    {
        /*
        try {
        */
        $classes = branchWise(SmClass::get());

        return view('backEnd.systemSettings.feesCarryForwardLogView', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function feesCarryForwardLogSearch()
    {
        /*
        try {
        */
        $logs = branchWise(FeesCarryForwardLog::with('studentRecord', 'studentRecord.studentDetail')
            ->when(request()->student_id, function ($s): void {
                $s->where('student_record_id', request()->student_id);
            })
            ->where('type', request()->fees_type));

        return DataTables::of($logs)
            ->addIndexColumn()
            ->editColumn('student_id', function ($log) {
                return $log->studentRecord->studentDetail->full_name ?? '';
            })
            ->addColumn('note', function ($log) {
                return $log->note ?? '';
            })
            ->addColumn('amount', function ($log) {
                return currency_format($log->amount ?? '');
            })
            ->editColumn('date', function ($log) {
                return dateConvert($log->date);
            })
            ->toJson();
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }
}
