<?php

namespace App\Http\Controllers;

use App\Models\DueFeesLoginPrevent;
use App\Models\SmAddExpense;
use App\Models\SmAddIncome;
use App\Models\SmCalendarSetting;
use App\Models\SmEvent;
use App\Models\SmHoliday;
use App\Models\SmNoticeBoard;
use App\Models\SmStaff;
use App\Models\SmStudent;
use App\Models\SmToDo;
use App\Support\GlobalVariable;
use App\Support\YearCheck;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Modules\Lead\Entities\LeadReminder;
use Modules\RolePermission\Entities\InfixRole;
use Modules\Saas\Entities\SmPackagePlan;
use Modules\Saas\Entities\SmSubscriptionPayment;
use Modules\Wallet\Entities\WalletTransaction;

class HomeController extends Controller
{
    public function dashboard()
    {

        $user = Auth::user();
        $role_id = $user->role_id;
        $is_due_fees_login_permission = generalSetting()->due_fees_login;
        $due_fees_login_prevent = DueFeesLoginPrevent::where('user_id', $user->id)->where('school_id', $user->school_id)->where('role_id', $role_id)->first();

        if (($user->role_id === 1) && ($user->is_administrator === 'yes') && (moduleStatusCheck('Saas') === true)) { // SuperAdmin
            return redirect('superadmin-dashboard');
        }

        if (($user->is_administrator === 'yes') && (moduleStatusCheck('Saas') === true) && (moduleStatusCheck('SaasHr') === true)) {
            return redirect('superadmin-dashboard');
        }

        if ($role_id === 2) { // Student
            if ($is_due_fees_login_permission === 1 && $due_fees_login_prevent !== null) {
                $errorMessage = '';
                Auth::logout();
                session(['role_id' => '']);
                Session::flash('toast_message', [
                    'type' => 'error', // 'success', 'info', 'warning', 'error'
                    'message' => 'Operation Failed, Unable to log in due to unpaid fees.',
                ]);

                return redirect('login')->withErrors(['custom_error' => $errorMessage]);
            }

            return redirect('student-dashboard');

        }
        if ($role_id === 3) { // Parent
            if ($is_due_fees_login_permission === 1 && $due_fees_login_prevent !== null) {
                $errorMessage = '';
                Auth::logout();
                session(['role_id' => '']);
                Session::flash('toast_message', [
                    'type' => 'error', // 'success', 'info', 'warning', 'error'
                    'message' => 'Operation Failed, Unable to log in due to unpaid fees.',
                ]);

                return redirect('login')->withErrors(['custom_error' => $errorMessage]);
            }

            return redirect('parent-dashboard');

        }
        if ($role_id === GlobalVariable::isAlumni()) { // Alumni
            return redirect('alumni-dashboard');
        }
        if ($role_id === '') {
            return redirect('login');
        }
        if ($role_id === 8) { // Librarian
            return redirect('book-list');
        }
        if ($role_id === 9) { // Bus driver
            return redirect('vehicle');
        }
        if ($role_id === 10) { // Partner
            return redirect('admin-dashboard');
        }
        if (Auth::user()->is_saas === 1) {
            return redirect('saasStaffDashboard');
        }

        return redirect('admin-dashboard');

    }

    // for display dashboard
    public function index(Request $request)
    {
        $chart_data = ' ';
        $academic_id = getAcademicId();
        $school_id = Auth::user()->school_id;
        $today = date('Y-m-d');
        $yearStart = date('Y').'-01-01';
        $monthStart = date('Y-m-01');

        $incomeBase = SmAddIncome::where('academic_id', $academic_id)
            ->where('name', '!=', 'Fund Transfer')
            ->where('school_id', $school_id)
            ->where('active_status', 1)
            ->whereBetween('date', [$yearStart, $today]);

        $expenseBase = SmAddExpense::where('academic_id', $academic_id)
            ->where('name', '!=', 'Fund Transfer')
            ->where('school_id', $school_id)
            ->where('active_status', 1)
            ->whereBetween('date', [$yearStart, $today]);

        $dailyIncomes = (clone $incomeBase)
            ->selectRaw('DATE(`date`) as period, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(`date`)'))
            ->pluck('total', 'period');

        $dailyExpenses = (clone $expenseBase)
            ->selectRaw('DATE(`date`) as period, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(`date`)'))
            ->pluck('total', 'period');

        $m_total_income = (clone $incomeBase)->where('date', '>=', $monthStart)->sum('amount');
        $m_total_expense = (clone $expenseBase)->where('date', '>=', $monthStart)->sum('amount');

        for ($i = 1; $i <= date('d'); $i++) {
            $day = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $period = date('Y-m-').$day;
            $income = (float) ($dailyIncomes[$period] ?? 0);
            $expense = (float) ($dailyExpenses[$period] ?? 0);

            $chart_data .= "{ day: '".$day."', income: ".$income.', expense:'.$expense.' },';
        }

        $monthlyIncomes = (clone $incomeBase)
            ->selectRaw('MONTH(`date`) as period, SUM(amount) as total')
            ->groupBy(DB::raw('MONTH(`date`)'))
            ->pluck('total', 'period');

        $monthlyExpenses = (clone $expenseBase)
            ->selectRaw('MONTH(`date`) as period, SUM(amount) as total')
            ->groupBy(DB::raw('MONTH(`date`)'))
            ->pluck('total', 'period');

        $chart_data_yearly = '';
        for ($i = 1; $i <= date('m'); $i++) {
            $month = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $yearlyIncome = (float) ($monthlyIncomes[$i] ?? 0);
            $yearlyExpense = (float) ($monthlyExpenses[$i] ?? 0);
            $chart_data_yearly .= "{ y: '".$month."', income: ".$yearlyIncome.', expense:'.$yearlyExpense.' },';
        }

        $count_event = 0;
        $SaasSubscription = isSubscriptionEnabled();
        $saas = moduleStatusCheck('Saas');
        if ($saas && $SaasSubscription && ! SmPackagePlan::isSubscriptionAutheticate()) {
            return redirect('subscription/package-list');
        }

        $user_id = Auth::id();
        if (isSubscriptionEnabled()) {
            $last_payment = SmSubscriptionPayment::where('school_id', Auth::user()->school_id)
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->where('approve_status', '=', 'approved')
                ->latest()->first();
            $package_info = [];

            if ($last_payment) {
                $package = SmPackagePlan::find($last_payment->package_id);

                $total_days = $package->payment_type === 'trial' ? $package->trial_days : $package->duration_days;
                $now_time = date('Y-m-d');
                $now_time = date('Y-m-d', strtotime($now_time.' + 1 days'));
                $end_date = date('Y-m-d', strtotime($last_payment->end_date));

                $formatted_dt1 = Carbon::parse($now_time);
                $formatted_dt2 = Carbon::parse($last_payment->end_date);
                $remain_days = $formatted_dt1->diffInDays($formatted_dt2);

                $package_info['package_name'] = $package->name;
                $package_info['student_quantity'] = $package->student_quantity;
                $package_info['staff_quantity'] = $package->staff_quantity;
                $package_info['remaining_days'] = $remain_days;
                $package_info['expire_date'] = date('Y-m-d', strtotime($last_payment->end_date.' + 1 days'));
            }

        }

        // for current month start
        if (moduleStatusCheck('Wallet')) {
            $monthlyWalletBalance = $this->showWalletBalance('diposit', 'refund', 'expense', 'fees_refund', 'Y-m-', $school_id);
        }

        // for current month end

        // for current year start
        $y_total_income = (clone $incomeBase)->sum('amount');
        $y_total_expense = (clone $expenseBase)->sum('amount');

        if (moduleStatusCheck('Wallet')) {
            $yearlyWalletBalance = $this->showWalletBalance('diposit', 'refund', 'expense', 'fees_refund', 'Y-', $school_id);
        }

        // for current year end

        if (Auth::user()->role_id === 4) {
            $events = SmEvent::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $school_id)
                ->where(function ($q): void {
                    $q->where('for_whom', 'All')->orWhere('for_whom', 'Teacher');
                })
                ->get();
        } else {
            $events = SmEvent::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->where('for_whom', 'All')
                ->get();
        }

        $staffs = SmStaff::where('school_id', $school_id)
            ->where('active_status', 1);

        $holidays = SmHoliday::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $school_id)
            ->get();

        $calendar_events = [];
        foreach ($holidays as $k => $holiday) {
            $calendar_events[$k]['title'] = $holiday->holiday_title;
            $calendar_events[$k]['start'] = $holiday->from_date;
            $calendar_events[$k]['end'] = Carbon::parse($holiday->to_date)->addDays(1)->format('Y-m-d');
            $calendar_events[$k]['description'] = $holiday->details;
            $calendar_events[$k]['url'] = $holiday->upload_image_file;
            $count_event = $k;
            $count_event++;
        }

        foreach ($events as $event) {
            $calendar_events[$count_event]['title'] = $event->event_title;
            $calendar_events[$count_event]['start'] = $event->from_date;
            $calendar_events[$count_event]['end'] = Carbon::parse($event->to_date)->addDays(1)->format('Y-m-d');
            $calendar_events[$count_event]['description'] = $event->event_des;
            $calendar_events[$count_event]['url'] = $event->uplad_image_file;
            $count_event++;
        }

        // added by abu nayem -for lead
        if (moduleStatusCheck('Lead') === true) {
            $reminders = LeadReminder::with('lead:first_name,last_name,id')->where('academic_id', $academic_id)
                ->where('school_id', $school_id)
                ->when(auth()->user()->role_id !== 1 && auth()->user()->staff, function ($q): void {
                    $q->where('reminder_to', auth()->user()->staff->id);
                })->get();
            foreach ($reminders as $reminder) {
                $calendar_events[$count_event]['title'] = 'Lead Reminder';
                $calendar_events[$count_event]['start'] = Carbon::parse($reminder->date_time)->format('Y-m-d').' '.$reminder->time;
                $calendar_events[$count_event]['end'] = Carbon::parse($reminder->date_time)->format('Y-m-d');
                $calendar_events[$count_event]['description'] = view('lead::lead_calender', ['event' => $reminder])->render();
                $calendar_events[$count_event]['url'] = 'lead/show/'.$reminder->id;
                $count_event++;
            }
        }

        // end lead reminder

        $notices = SmNoticeBoard::query();
        $notices->where('active_status', 1)->where('academic_id', $academic_id)->where('school_id', $school_id)->where('publish_on', '<=', date('Y-m-d'));
        $notices->when(auth()->user()->role_id !== 1, function ($query): void {
            $query->where('inform_to', 'LIKE', '%'.auth()->user()->role_id.'%');
        });
        $notices = $notices->get();

        $staffs = $staffs->where('role_id', '!=', 1)
            ->where('school_id', $school_id);
        $students = SmStudent::where('active_status', 1)
            ->where('school_id', $school_id);
        $data = [
            'totalStudents' => (clone $students)->count(),
            'totalParents' => (clone $students)->whereNotNull('parent_id')->distinct()->count('parent_id'),

            'totalTeachers' => (clone $staffs)->where('role_id', 4)->count(),

            'totalStaffs' => (clone $staffs)->count(),

            'toDos' => SmToDo::where('created_by', $user_id)
                ->where('school_id', $school_id)
                ->get(),

            'notices' => $notices,

            // where('inform_to', 'LIKE', '%2%')

            'm_total_income' => $m_total_income,
            'y_total_income' => $y_total_income,
            'm_total_expense' => $m_total_expense,
            'y_total_expense' => $y_total_expense,
            'holidays' => $holidays,
            'events' => $events,

            'year' => YearCheck::getYear(),
        ];
        if (moduleStatusCheck('Wallet')) {
            $data['monthlyWalletBalance'] = $monthlyWalletBalance;
            $data['yearlyWalletBalance'] = $yearlyWalletBalance;
        }

        if (Session::has('info_check')) {
            session(['info_check' => 'no']);
        } else {
            session(['info_check' => 'yes']);
        }

        $data['settings'] = SmCalendarSetting::get();
        $data['roles'] = InfixRole::where('is_saas', 0)->where(function ($q): void {
            $q->where('school_id', auth()->user()->school_id)->orWhere('type', 'System');
        })
            ->whereNotIn('id', [1, 2])
            ->get();
        $academicCalendar = new SmAcademicCalendarController();
        $branchId = moduleStatusCheck('Branch') ? userBranch() : 'none';
        $calendarCacheKey = 'dashboard_academic_calendar_user_'.$user_id.'_school_'.$school_id.'_academic_'.$academic_id.'_branch_'.$branchId.'_role_'.Auth::user()->role_id;
        $data['events'] = Cache::remember($calendarCacheKey, 60, fn () => $academicCalendar->calenderData());
        if (isSubscriptionEnabled()) {
            return view('backEnd.dashboard', compact('chart_data', 'chart_data_yearly', 'calendar_events', 'package_info'))->with($data);
        }

        return view('backEnd.dashboard', compact('chart_data', 'chart_data_yearly', 'calendar_events'))->with($data);

    }

    public function saveToDoData(Request $request)
    {

        $toDolists = new SmToDo();
        $toDolists->todo_title = $request->todo_title;
        $toDolists->date = date('Y-m-d', strtotime($request->date));
        $toDolists->created_by = Auth()->user()->id;
        $toDolists->school_id = Auth()->user()->school_id;
        $toDolists->academic_id = getAcademicId();
        $results = $toDolists->save();

        if ($results) {
            Toastr::success('Operation successful', 'Success');

            return redirect()->back();
        }
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();

    }

    public function viewToDo($id)
    {

        if (checkAdmin()) {
            $toDolists = SmToDo::find($id);
        } else {
            $toDolists = SmToDo::where('id', $id)->where('school_id', Auth::user()->school_id)->first();
        }

        return view('backEnd.dashboard.viewToDo', compact('toDolists'));

    }

    public function editToDo($id)
    {
        if (checkAdmin() === true) {
            $editData = SmToDo::find($id);
        } else {
            $editData = SmToDo::where('id', $id)->where('school_id', Auth::user()->school_id)->first();
        }

        return view('backEnd.dashboard.editToDo', compact('editData', 'id'));

    }

    public function updateToDo(Request $request)
    {

        $to_do_id = $request->to_do_id;
        $toDolists = SmToDo::find($to_do_id);
        $toDolists->todo_title = $request->todo_title;
        $toDolists->date = date('Y-m-d', strtotime($request->date));
        $toDolists->complete_status = $request->complete_status;
        $toDolists->updated_by = Auth()->user()->id;
        $results = $toDolists->update();

        if ($results) {
            Toastr::success('Operation successful', 'Success');

            return redirect()->back();
        }
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();

    }

    public function removeToDo(Request $request)
    {

        $to_do = SmToDo::find($request->id);
        $to_do->complete_status = 'C';
        $to_do->academic_id = getAcademicId();
        $to_do->save();
        $html = '';

        return response()->json('html');

    }

    public function getToDoList(Request $request)
    {

        $to_do_list = SmToDo::where('complete_status', 'C')->where('school_id', Auth::user()->school_id)->get();
        $datas = [];
        foreach ($to_do_list as $to_do) {
            $datas[] = [
                'title' => $to_do->todo_title,
                'date' => date('jS M, Y', strtotime($to_do->date)),
            ];
        }

        return response()->json($datas);

    }

    public function viewNotice($id)
    {

        $notice = SmNoticeBoard::find($id);

        return view('backEnd.dashboard.view_notice', compact('notice'));

    }

    public function updatePassowrd()
    {
        return view('backEnd.update_password');
    }

    public function updatePassowrdStore(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|same:confirm_password|min:6|different:current_password',
            'confirm_password' => 'required|min:6',
        ]);

        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $result = $user->save();
            if ($result) {
                Toastr::success('Operation successful', 'Success');

                return redirect()->back();
            }
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }
        Toastr::error('Current password not match!', 'Failed');

        return redirect()->back();

    }

    public function userCustomMenu($slug = null)
    {
        if (moduleStatusCheck('CustomMenu') === false) {
            abort(404);
        }
        $custom_menus = \Modules\CustomMenu\Entities\CustomMenu::where('active_status', 1)->where('slug', $slug)->first();
        if (! empty($custom_menus) && $custom_menus->menu_type === 'url') {
            return redirect()->to($custom_menus->url_link);
        }
        $menu_item = $custom_menus;

        return view('backEnd.userCustomMenu.index', ['menu_item' => $menu_item]);
    }

    public function domainValidate(Request $request)
    {

        $request->validate([
            'domain' => 'required|string|max:100|regex:/^[a-zA-Z0-9]+$/|unique:sm_schools,domain',
        ]);

        return response()->json(['valid' => true]);
    }

    private function showWalletBalance(string $diposit, string $refund, string $expense, string $feesRefund, string $date, $school_id)
    {

        $walletTranscations = WalletTransaction::where('status', 'approve')
            ->where('updated_at', 'like', date($date).'%')
            ->where('school_id', $school_id)
            ->get();
        $totalWalletBalance = $walletTranscations->where('type', $diposit)->sum('amount');
        $totalWalletRefundBalance = $walletTranscations->where('type', $refund)->sum('amount');
        $totalWalletExpenseBalance = $walletTranscations->where('type', $expense)->sum('amount');
        $totalFeesRefund = $walletTranscations->where('type', $feesRefund)->sum('amount');

        return ($totalWalletBalance - $totalWalletExpenseBalance) - $totalWalletRefundBalance + $totalFeesRefund;
    }
}
