<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SmAcademicYear;
use App\Models\SmGeneralSettings;
use App\Models\SmSchool;
use App\Models\SmStyle;
use App\Models\Theme;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_code' => 'required|string|max:255|unique:sm_schools,school_code',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $schoolCode = Str::lower($request->school_code);

        $school = SmSchool::create([
            'school_name' => $request->school_name,
            'school_code' => $schoolCode,
            'domain' => $schoolCode,
            'email' => $request->email,
            'active_status' => 1,
            'is_enabled' => 1,
            'created_by' => 1,
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->email,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
            'usertype' => 'admin',
            'school_id' => $school->id,
            'is_saas' => 1,
            'active_status' => 1,
            'access_status' => 1,
        ]);

        $academicYear = SmAcademicYear::create([
            'year' => date('Y'),
            'title' => date('Y'),
            'starting_date' => date('Y-m-d'),
            'ending_date' => date('Y-12-31'),
            'active_status' => 1,
            'school_id' => $school->id,
            'created_by' => $user->id,
        ]);

        Theme::create([
            'title' => 'Default Theme',
            'is_default' => 1,
            'is_system' => 1,
            'school_id' => $school->id,
            'created_by' => $user->id,
        ]);

        SmStyle::create([
            'style_name' => 'Default',
            'is_default' => 1,
            'is_active' => 1,
            'active_status' => 1,
            'school_id' => $school->id,
            'created_by' => $user->id,
        ]);

        SmGeneralSettings::create([
            'school_name' => $request->school_name,
            'school_code' => $schoolCode,
            'email' => $request->email,
            'session_id' => $academicYear->id,
            'active_status' => 1,
            'school_id' => $school->id,
        ]);

        Auth::login($user);

        return redirect()->route('subscription.plans');
    }
}
