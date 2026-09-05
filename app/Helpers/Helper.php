<?php

/**
 * Helper.php — Core global helper functions.
 *
 * WHAT'S IN THIS FILE (117 remaining functions):
 *   - Settings & config:   generalSetting(), getAcademicId(), allStyles(), activeTheme()
 *   - Date & locale:       dateConvert(), systemDateFormat(), timeZone(), _trans()
 *   - Auth & permissions:  userPermission(), checkAdmin(), has_permission(), getPermissions()
 *   - Module/plugin:       moduleStatusCheck(), moduleVersion(), SchoolModuleStatus()
 *   - File & media:        fileUpload(), fileUpdate(), uploadPath(), profile(), get_logo()
 *   - Notification:        sendNotification(), send_notification(), apk_secret()
 *   - SaaS:                getDomainName(), SaasSchool(), SaasDomain(), saasEnv()
 *   - UI:                  toastrError(), toastrSuccess(), ad(), validationMessage()
 *   - Misc utilities:      generateQRCode(), dayNames(), generateRandomString(), etc.
 *
 * WHAT WAS EXTRACTED IN P4 (moved to separate files in composer.json autoload.files):
 *   - app/Helpers/ExamHelper.php  — 37 exam/result/grade functions
 *   - app/Helpers/FeesHelper.php  — 17 fees/payment/invoice functions
 *   - app/Helpers/SmsHelper.php   — 7 SMS gateway functions
 *   - app/Helpers/EmailHelper.php — 3 email sending functions
 *   - app/Helpers/saas.php        — SaaS-specific helpers
 *   - app/Helpers/Basic.php       — Core data fetching helpers
 *
 * All files are autoloaded via composer.json autoload.files — no require() needed.
 */

use App\Models\AllExamWisePosition;
use App\Models\ExamMeritPosition;
use App\Models\Shift;
use App\Models\SmAcademicYear;
use App\Models\SmCalendarSetting;
use App\Models\SmClassTeacher;
use App\Models\SmDateFormat;
use App\Models\SmExam;
use App\Models\SmExamAttendance;
use App\Models\SmExamAttendanceChild;
use App\Models\SmExamSetup;
use App\Models\SmGeneralSettings;
use App\Models\SmHeaderMenuManager;
use App\Models\SmLanguage;
use App\Models\SmParent;
use App\Models\SmSchool;
use App\Models\SmSmsGateway;
use App\Models\SmStaff;
use App\Models\SmsTemplate;
use App\Models\SmStudent;
use App\Models\SmStudentRegistrationField;
use App\Models\SmStyle;
use App\Models\SmSubject;
use App\Models\StudentRecord;
use App\User;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Clickatell\Rest;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Larabuild\Pagebuilder\Models\Page;
use Modules\Branch\Entities\Branch;
use Modules\Forum\Entities\ForumSetting;
use Modules\Lms\Entities\CourseSetting;
use Modules\MenuManage\Entities\MenuManage;
use Modules\ParentRegistration\Entities\SmStudentRegistration;
use Modules\QRCodeAttendance\Entities\QRCodeAttendanceSetting;
use Modules\RolePermission\Entities\Permission;
use Modules\University\Entities\UnAcademicYear;

function sendEmailBio(array $data, $to_name, $to_email, $email_sms_title)
{
    $systemSetting = DB::table('sm_general_settings')->select('school_name', 'email')->find(1);
    $systemEmail = DB::table('sm_email_settings')->find(1);
    $system_email = $systemEmail->from_email;
    $school_name = $systemSetting->school_name;
    if (! empty($system_email)) {
        $data['email_sms_title'] = $email_sms_title;
        $data['system_email'] = $system_email;
        $data['school_name'] = $school_name;
        $details = $to_email;
        dispatch(new \App\Jobs\SendEmailJob($data, $details));
        $error_data = [];

        return true;
    }

    $error_data[0] = 'success';
    $error_data[1] = 'Operation Failed, Please Updated System Mail';

    return $error_data;

}

if (! function_exists('youtubeVideo')) {
    function youtubeVideo($video_url)
    {
        if (Str::contains($video_url, 'youtu.be')) {
            $url = explode('/', $video_url);

            return 'https://www.youtube.com/watch?v='.$url[3];
        }

        if (Str::contains($video_url, '&')) {
            return mb_substr($video_url, 0, mb_strpos($video_url, '&'));
        }

        return $video_url;

    }
}

function showFileName($data): string
{
    $name = explode('/', $data);
    $number = array_key_last($name);

    return $name[$number];
}

function sendSMSApi(string $to_mobile, string $sms, $id)
{
    $activeSmsGateway = SmSmsGateway::find($id);
    if ($activeSmsGateway->gateway_name === 'Twilio') {
        if (! $activeSmsGateway->twilio_account_sid || $activeSmsGateway->twilio_authentication_token) {
            return null;
        }

        $client = new Twilio\Rest\Client($activeSmsGateway->twilio_account_sid, $activeSmsGateway->twilio_authentication_token);
        if ($to_mobile  && $to_mobile !== '0') {
            return $message = $client->messages->create($to_mobile, ['from' => $activeSmsGateway->twilio_registered_no, 'body' => $sms]);
        }
    } // end Twilio
    elseif ($activeSmsGateway->gateway_name === 'Clickatell') {

        // config(['clickatell.api_key' => $activeSmsGateway->clickatell_api_id]); //set a variale in config file(clickatell.php)

        $clickatell = new Rest();
        $result = $clickatell->sendMessage(['to' => $to_mobile, 'content' => $sms]);
    } // end Clickatell

    // start Himalayasms

    elseif ($activeSmsGateway->gateway_name === 'Himalayasms') {
        $client = new Client();
        $request = $client->get('https://sms.techhimalaya.com/base/smsapi/index.php', [
            'query' => [
                'key' => $activeSmsGateway->himalayasms_key,
                'senderid' => $activeSmsGateway->himalayasms_senderId,
                'campaign' => $activeSmsGateway->himalayasms_campaign,
                'routeid' => $activeSmsGateway->himalayasms_routeId,
                'contacts' => $to_mobile,
                'msg' => $sms,
                'type' => 'text',
            ],
            'http_errors' => false,
        ]);

        $result = $request->getBody();
    } elseif ($activeSmsGateway->gateway_name === 'Msg91') {
        $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
        $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
        $msg91_route = $activeSmsGateway->msg91_route;
        $msg91_country_code = $activeSmsGateway->msg91_country_code;

        $curl = curl_init();

        $url = 'https://api.msg91.com/api/sendhttp.php?mobiles='.$to_mobile.'&authkey='.$msg91_authentication_key_sid.'&route='.$msg91_route.'&sender='.$msg91_sender_id.'&message='.$sms.'&country=91';

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = $err  && $err !== '0' ? 'cURL Error #:'.$err : $response;
    }

    // end Msg91
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err  && $err !== '0') {
        return 'cURL Error #:'.$err;
    }

    return $response;
} // end Msg91

function sendSMSBio(string $to_mobile, string $sms)
{
    $activeSmsGateway = SmSmsGateway::where('school_id', Auth::user()->school_id)->where('active_status', '=', 1)->first();
    if ($activeSmsGateway->gateway_name === 'Twilio') {

        config(['TWILIO.SID' => $activeSmsGateway->twilio_account_sid]);
        config(['TWILIO.TOKEN' => $activeSmsGateway->twilio_authentication_token]);
        config(['TWILIO.FROM' => $activeSmsGateway->twilio_registered_no]);
        $account_id = $activeSmsGateway->twilio_account_sid; // Your Account SID from www.twilio.com/console
        $auth_token = $activeSmsGateway->twilio_authentication_token; // Your Auth Token from www.twilio.com/console
        $from_phone_number = $activeSmsGateway->twilio_registered_no;
        $client = new Twilio\Rest\Client($account_id, $auth_token);
        if ($to_mobile  && $to_mobile !== '0') {
            return $message = $client->messages->create($to_mobile, ['from' => $from_phone_number, 'body' => $sms]);
        }
    } // end Twilio
    elseif ($activeSmsGateway->gateway_name === 'Clickatell') {

        // config(['clickatell.api_key' => $activeSmsGateway->clickatell_api_id]); //set a variale in config file(clickatell.php)

        $clickatell = new Rest();
        $result = $clickatell->sendMessage(['to' => $to_mobile, 'content' => $sms]);
    } // end Clickatell

    elseif ($activeSmsGateway->gateway_name === 'Msg91') {
        $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
        $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
        $msg91_route = $activeSmsGateway->msg91_route;
        $msg91_country_code = $activeSmsGateway->msg91_country_code;

        $curl = curl_init();

        $url = 'https://api.msg91.com/api/sendhttp.php?mobiles='.$to_mobile.'&authkey='.$msg91_authentication_key_sid.'&route='.$msg91_route.'&sender='.$msg91_sender_id.'&message='.$sms.'&country=91';

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = $err  && $err !== '0' ? 'cURL Error #:'.$err : $response;
    } // end Msg91
    elseif ($activeSmsGateway->gateway_name === 'TextLocal') {

        // Account details
        // $apiKey = urlencode('Your apiKey');
        $apiKey = $activeSmsGateway->textlocal_hash;
        $url = $activeSmsGateway->type === 'in' ? 'http://api.textlocal.in/send/' : 'http://api.txtlocal.com/send/';
        // Message details
        $numbers = $to_mobile;
        $sender = urlencode($activeSmsGateway->textlocal_sender);
        $message = rawurlencode($sms);

        // $numbers = implode(',', $numbers);

        // Prepare data for POST request
        $data = ['apikey' => $apiKey, 'numbers' => $numbers, 'sender' => $sender, 'message' => $message];

        // Send the POST request with cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Process your response here
        $result = $response;
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err  && $err !== '0') {
        return 'cURL Error #:'.$err;
    }

    return $response;
} // end Msg91

function getValueByString($student_id, $string, $extra = null)
{
    $student = SmStudent::find($student_id);
    if ($extra !== null) {
        return $student->$string->$extra;
    }

    return $student->$string;

}

function getParentName($student_id, $string, $extra = null)
{
    $student = SmStudent::find($student_id);
    $parent = SmParent::where('id', $student->parent_id)->first();
    if ($extra !== null) {
        return $student->$parent->$extra;
    }

    return $parent->fathers_name;

}

function SMSBody($body, $s_id, $time)
{
    try {
        $original_message = $body;
        // $original_message= "Dear Parent [fathers_name], your child [class] came to the school at [section]";
        $chars = preg_split('/[\s,]+/', $original_message, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($chars as $char) {
            if (mb_strstr($char[0], '[')) {
                $str = str_replace('[', '', $char);
                $str = str_replace(']', '', $str);
                $str = str_replace('.', '', $str);
                if ($str === 'class') {
                    $str = 'class';
                    $extra = 'class_name';
                    $custom_array[$char] = getValueByString($s_id, $str, $extra);
                } elseif ($str === 'section') {
                    $str = 'section';
                    $extra = 'section_name';
                    $custom_array[$char] = getValueByString($s_id, $str, $extra);
                } elseif ($str === 'check_in_time') {
                    $custom_array[$char] = $time;
                } elseif ($str === 'fathers_name') {
                    $str = 'parents';
                    $extra = 'fathers_name';
                    $custom_array[$char] = getValueByString($s_id, $str, $extra);
                    // $custom_array[$item]= 'father';
                } else {
                    $custom_array[$char] = getValueByString($s_id, $str);
                }
            }
        }

        foreach ($custom_array as $key => $value) {
            $original_message = str_replace($key, $value, $original_message);
        }

        return $original_message;
    } catch (Exception $exception) {
        return [];
    }
}

function FeesDueSMSBody($body, $s_id, $time)
{
    try {
        $original_message = $body;
        $chars = preg_split('/[\s,]+/', $original_message, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($chars as $char) {
            if (mb_strstr($char[0], '|')) {
                $str = str_replace('|', '', $char);
                // return $str;
                $str = str_replace('|', '', $str);
                $str = str_replace('.', '', $str);
                if ($str === 'StudentName') {
                    $str = 'StudentName';
                    $extra = 'full_name';
                    $custom_array[$char] = getValueByString($s_id, $str, $extra);
                } elseif ($str === 'fathers_name') {
                    $str = 'parents';
                    $extra = 'fathers_name';
                    $custom_array[$char] = getValueByString($s_id, $str, $extra);
                    // $custom_array[$item]= 'father';
                } else {
                    $custom_array[$char] = getValueByString($s_id, $str);
                }
            }
        }

        foreach ($custom_array as $key => $value) {
            $original_message = str_replace($key, $value, $original_message);
        }

        return $original_message;
    } catch (Exception $exception) {
        return [];
    }
}

if (! function_exists('userPermission')) {
    function userPermission($route, $role_id = null, $purpose = null): bool
    {

        $role_id = Auth::user()->role_id;
        $permissions = app('permission');
        if ($role_id === 1 && Auth::user()->is_administrator === 'yes') {
            return true;
        }
        if ((! empty($permissions)) && ($role_id !== 1)) {
            return @in_array($route, $permissions);
        }

        if (moduleStatusCheck('Saas') === true) {
            $saas_status_ids = app('saasSettings');

            return ! @in_array($route, $saas_status_ids);
        }

        return true;

    }
}

if (! function_exists('moduleStatusCheck')) {
    /**
     * Check whether a module is active.
     *
     * OPTIMIZED: Now delegates to ModuleRegistry which computes all module statuses
     * ONCE per request and serves subsequent calls from a static in-memory array.
     *
     * Previously this function was called 2,236+ times per request, each time:
     *   - Reading from session
     *   - Querying InfixModuleManager DB table
     *   - Checking file_exists() on the module provider path
     *
     * Now: first call boots the registry (one DB query for all modules), all subsequent
     * calls are pure array lookups — zero DB, zero filesystem.
     *
     * @param  string  $module  Module name (e.g. 'University', 'Saas', 'Fees')
     */
    function moduleStatusCheck(string $module): bool
    {
        return \App\Support\ModuleRegistry::isActive($module);
    }
}
if (! function_exists('dateConvert')) {

    function dateConvert($input_date)
    {
        try {
            $system_date_format = session()->get('system_date_format');
            if (empty($system_date_format)) {
                $date_format_id = SmGeneralSettings::where('id', 1)->first(['date_format_id'])->date_format_id;
                $system_date_format = SmDateFormat::where('id', $date_format_id)->first(['format'])->format;
                session()->put('system_date_format', $system_date_format);
            }

            return \Carbon\Carbon::parse($input_date)->format($system_date_format);
        } catch (Throwable $throwable) {
            return $input_date;
        }
    }
}

if (! function_exists('dateTimeConvert')) {

    function dateTimeConvert($input_date_time)
    {
        try {
            $system_date_format = session()->get('system_date_format').' g:i A';
            if ($system_date_format === '0') {
                $date_format_id = SmGeneralSettings::where('id', 1)->first(['date_format_id'])->date_format_id;
                $system_date_format = SmDateFormat::where('id', $date_format_id)->first(['format'])->format.' g:i A';
                session()->put('system_date_format', $system_date_format);
            }

            return \Carbon\Carbon::parse($input_date_time)->format($system_date_format);
        } catch (Throwable $throwable) {
            return $input_date_time;
        }
    }
}

if (! function_exists('convertTime')) {
    function convertTime($time): string
    {
        return date('g:i A', strtotime($time));
    }
}

if (! function_exists('getAcademicId')) {
    function getAcademicId()
    {

        if (session()->has('sessionId') && is_numeric(session()->get('sessionId'))) {
            return (int) session()->get('sessionId');
        }

        session()->forget('sessionId');

        if (moduleStatusCheck('University')) {
            $session_id = generalSetting()->un_academic_id;
            if (! is_numeric($session_id) || ! $session_id) {
                $session_id = UnAcademicYear::where('school_id', Auth::user()->school_id)->where('active_status', 1)->first()?->id ?? 1;
            }
        } else {
            $session_id = generalSetting()?->session_id;

            if (! $session_id) {
                if(Auth::check()){
                    $session_id = SmAcademicYear::where('school_id', Auth::user()->school_id)->where('active_status', 1)->first()?->id;
                } else{
                    $session_id = 1;
                }
            }
        }

        if (! is_numeric($session_id)) {
            $session_id = moduleStatusCheck('University')
                ? (UnAcademicYear::where('school_id', Auth::user()->school_id)->where('active_status', 1)->first()?->id ?? 1)
                : (SmAcademicYear::where('school_id', Auth::user()->school_id)->where('active_status', 1)->first()?->id ?? 1);
        }

        session()->put('sessionId', $session_id);

        return session()->get('sessionId');

    }
}

if (! function_exists('timeZone')) {
    function timeZone()
    {
        $time_zone_setup = session()->get('time_zone_setup');
        if (is_null($time_zone_setup)) {
            $time_zone = SmGeneralSettings::join('sm_time_zones', 'sm_time_zones.id', '=', 'sm_general_settings.time_zone_id')
                ->where('school_id', 1)->first('time_zone');
            session()->put('time_zone_setup', $time_zone);
            $time_zone_setup = session()->get('time_zone_setup');
        }

        return $time_zone_setup->time_zone;
    }
}

if (! function_exists('schoolTimeZone')) {
    function schoolTimeZone()
    {
        $time_zone_setup = session()->get('time_zone_setup');
        if (is_null($time_zone_setup)) {
            $time_zone = SmGeneralSettings::join('sm_time_zones', 'sm_time_zones.id', '=', 'sm_general_settings.time_zone_id')
                ->where('school_id', Auth::user()->school_id)->first('time_zone');
            session()->put('time_zone_setup', $time_zone);
            $time_zone_setup = session()->get('time_zone_setup');
        }

        return $time_zone_setup->time_zone;
    }
}

if (! function_exists('getUserLanguage')) {
    function getUserLanguage()
    {

        if (Auth::check()) {
            return userLanguage();
        }

        $school_id = app()->bound('school') ? app('school')->id : 1;
        return Cache::remember('school_lang_'.$school_id, 60, function () use ($school_id) {
            $user = User::where('role_id', 1)->where('school_id', $school_id)->first();
            $lang = $user ? $user->language : 'en';
            return $lang;
        });


    }
}

if (! function_exists('checkAdmin')) {
    function checkAdmin(): ?bool
    {
        if (Auth::check()) {
            if (Auth::user()->is_administrator === 'yes') {
                return true;
            }

            return Auth::user()->is_saas === 1;
        }

        return null;
    }
}

if (! function_exists('getTempleteDetails')) {
    function getTempleteDetails($purpose, $type = null)
    {
        $data = SmsTemplate::query();
        $data = $data->where('purpose', $purpose)->where('status', 1);
        if ($type) {
            $data->where('type', $type);
        }

        if (Auth::check()) {
            return $data->where('school_id', Auth::user()->school_id)->first();
        }

        return $data->first();
    }
}

if (! function_exists('getFileName')) {
    function getFileName($data): string
    {
        if ($data) {
            $name = explode('/', $data);

            return $name[count($name) - 1] ?? $name[0];
        }

        return '';

    }
}

// Get File Path From HELPER

if (! function_exists('getFilePath3')) {
    function getFilePath3($data): string
    {

        if ($data) {
            $name = explode('/', $data);

            return $name[3] ?? $name[0];
        }

        return '';

    }
}

if (! function_exists('getFilePath4')) {
    function getFilePath4($data): string
    {
        if ($data) {
            $name = explode('/', $data);
            if ($name[4]  && $name[4] !== '0') {
                return $name[3];
            }

            return '';

        }

        return '';

    }
}

if (! function_exists('getFilePath5')) {
    function getFilePath5($data): string
    {
        if ($data) {
            $name = explode('/', $data);
            if ($name[5]  && $name[5] !== '0') {
                return $name[5];
            }

            return '';

        }

        return '';

    }
}

if (! function_exists('showPicName')) {
    function showPicName($data): ?string
    {
        try {
            if ($data) {
                $name = explode('/', $data);
                if ($name[4]  && $name[4] !== '0') {
                    return $name[4];
                }

                return '';

            }

            return '';

        } catch (Exception $exception) {
            return null;
        }
    }
}

if (! function_exists('showJoiningLetter')) {
    function showJoiningLetter($data): string
    {
        $name = explode('/', $data);

        return $name[3];
    }
}

if (! function_exists('showResume')) {
    function showResume($data): string
    {
        $name = explode('/', $data);

        return $name[3];
    }
}

if (! function_exists('showDocument')) {
    function showDocument($data): string
    {
        @$name = explode('/', @$data);
        if (@$name[4]  && @$name[4] !== '0') {

            return $name[4];
        }

        return '';

    }
}

// end get file path from helpers

if (! function_exists('getNumberOfPart')) {
    function getNumberOfPart($subject_id, $class_id, $section_id, $exam_term_id)
    {
        try {
            return SmExamSetup::where([
                ['class_id', $class_id],
                ['subject_id', $subject_id],
                ['section_id', $section_id],
                ['exam_term_id', $exam_term_id],
            ])->get();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('teacherAssignedClass')) {
    function teacherAssignedClass()
    {
        try {
            $class_id = [];
            $role_id = Auth::user()->role_id;
            if ($role_id === 4) {
                $classes = SmClassTeacher::where('teacher_id', Auth::user()->id)->get(['id']);
                foreach ($classes as $class) {
                    $class_id[] = $class->module_id;
                }
            } else {

                $general_setting = SmGeneralSettings::where('school_id', auth()->user()->school_id)->first();

                return @$general_setting->school_name;
            }
        } catch (Exception $exception) {
            return $class_id = [];
        }

        return null;
    }
}

if (! function_exists('getValueByStringTestRegistration')) {
    function getValueByStringTestRegistration(array $data, $str)
    {
        if ($str === 'password') {
            return '123456';
        }

        if ($str === 'school_name') {
            if (moduleStatusCheck('Saas') === true) {
                $student_info = SmStudentRegistration::find(@$data['id']);

                return @$student_info->school->school_name;
            }

            $general_setting = SmGeneralSettings::find(1);

            return @$general_setting->school_name;

        }

        if ($data['slug'] === 'student') {
            $student_info = SmStudentRegistration::find(@$data['id']);
            if ($str === 'name') {
                return @$student_info->first_name.' '.@$student_info->last_name;
            }

            if ($str === 'guardian_name') {
                return @$student_info->guardian_name;
            }

            if ($str === 'class') {
                return @$student_info->class->class_name;
            }

            if ($str === 'section') {
                return @$student_info->section->section_name;
            }
        } elseif ($data['slug'] === 'parent') {
            $parent_info = SmStudentRegistration::find(@$data['id']);
            if ($str === 'name') {
                return @$parent_info->guardian_name;
            }

            if ($str === 'student_name') {
                return @$parent_info->first_name.' '.@$parent_info->last_name;
            }
        }

        return null;
    }
}

if (! function_exists('getValueByStringTestReset')) {
    function getValueByStringTestReset(array $data, $str)
    {
        if ($str === 'school_name') {

            $general_setting = SmGeneralSettings::where('school_id', auth()->user()->school_id)->first();

            return @$general_setting->school_name;
        }

        if ($str === 'name') {
            $user = User::where('email', $data['email'])->first();

            return @$user->full_name;
        }

        return null;
    }
}

if (! function_exists('is_absent_check')) {

    function is_absent_check($exam_id, $class_id, $section_id, $subject_id, $student_id)
    {
        try {
            $exam_attendance = SmExamAttendance::where('exam_id', $exam_id)->where('class_id', $class_id)->where('section_id', $section_id)->where('subject_id', $subject_id)->first();

            return SmExamAttendanceChild::where('exam_attendance_id', $exam_attendance->id)->where('student_id', $student_id)->first();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('generalSetting')) {
    /**
     * Get the general settings for the current school.
     *
     * OPTIMIZED: Replaced unreliable session caching with Cache::remember (file driver, 30 min TTL).
     * Session cache expired on logout and was unavailable in API/console contexts.
     * File cache persists across requests and is scoped per school_id for SaaS support.
     */
    function generalSetting()
    {
        if (app()->bound('school')) {
            $schoolId = app('school')->id;
        } elseif (request('school_id')) {
            $schoolId = (int) request('school_id');
        } elseif (Auth::check()) {
            $schoolId = Auth::user()->school_id;
        } else {
            $schoolId = 1;
        }

        static $settings = [];
        if (array_key_exists($schoolId, $settings)) {
            return $settings[$schoolId];
        }

        return $settings[$schoolId] = Cache::remember('general_settings_'.$schoolId, 1800, function () use ($schoolId) {
            return SmGeneralSettings::where('school_id', $schoolId)->with(['currencyDetail', 'timeZone'])->first();
        });
    }
}

if (! function_exists('systemDateFormat')) {
    /**
     * OPTIMIZED: Replaced session caching (with typo: 'system_date_foramt') with Cache::remember.
     * Also removed direct DB::table query — now reads from cached generalSetting() instead.
     */
    function systemDateFormat()
    {
        $schoolId = Auth::check() ? Auth::user()->school_id : 1;

        return Cache::remember('system_date_format_'.$schoolId, 1800, function () {
            $setting = generalSetting();

            return SmDateFormat::find($setting->date_format_id);
        });
    }
}

if (! function_exists('dashboardBackground')) {
    function dashboardBackground()
    {
        return app('dashboard_background');
    }
}

if (! function_exists('allStyles')) {
    function allStyles()
    {

        if (session()->has('all_styles')) {
            return session()->get('all_styles');
        }

        $all_styles = SmStyle::where('school_id', 1)->where('active_status', 1)->get();
        session()->put('all_styles', $all_styles);

        return session()->get('all_styles');

    }
}

if (! function_exists('textDirection')) {
    function textDirection()
    {

        if (session()->has('text_direction')) {
            return session()->get('text_direction');
        }

        $ttl_rtl = Auth::user()->rtl_ltl;
        session()->put('text_direction', $ttl_rtl);

        // return $ttl_rtl;
        return session()->get('text_direction');

    }
}

if (! function_exists('userRtlLtl')) {
    function userRtlLtl()
    {
        // return 1;
        static $direction = null;

        if ($direction !== null) {
            return $direction;
        }

        if (session()->has('user_text_direction')) {
            return $direction = session()->get('user_text_direction');
        }

        $school_id = app()->bound('school') ? app('school')->id : 1;
        $ttl_rtl = Cache::remember('school_text_direction_'.$school_id, 1800, function () use ($school_id) {
            $user = User::where('role_id', 1)->where('school_id', $school_id)->first(['rtl_ltl']);

            return $user ? $user->rtl_ltl : 2;
        });
        session()->put('user_text_direction', $ttl_rtl);

        return $direction = session()->get('user_text_direction');

    }
}

if (! function_exists('userLanguage')) {
    function userLanguage()
    {

        if (session()->has('user_language')) {
            return session()->get('user_language');
        }

        $language = Auth::user()->language;
        session()->put('user_language', $language);

        return session()->get('user_language');

    }
}

if (! function_exists('schoolConfig')) {
    function schoolConfig()
    {
        if (! app()->bound('school_general_settings')) {
            return null;
        }
        $value = app('school_general_settings');
        return $value ?: null;
    }
}

if (! function_exists('selectedLanguage')) {
    function selectedLanguage()
    {
        if (session()->has('selected_language')) {
            return session()->get('selected_language');
        }

        $selected_language = Auth::check() ? SmGeneralSettings::where('school_id', Auth::user()->school_id)->first() :
            DB::table('sm_general_settings')->where('school_id', 1)->first();
        session()->put('selected_language', $selected_language);

        return session()->get('selected_language');

    }
}

if (! function_exists('profile')) {
    function profile()
    {
        return auth()->user()->profile;
    }
}

if (! function_exists('getSession')) {
    function getSession()
    {
        if (session()->has('session')) {
            return session()->get('session');
        }

        $selected_language = Auth::check() ? SmGeneralSettings::where('school_id', Auth::user()->school_id)->first() :
            DB::table('sm_general_settings')->where('school_id', 1)->first();
        $session = DB::table('sm_academic_years')->where('id', $selected_language->session_id)->first();
        session()->put('session', $session);

        return session()->get('session');

    }
}

if (! function_exists('systemLanguage')) {
    function systemLanguage()
    {
        static $languages = [];

        $schoolId = auth()->user()->school_id;
        if (array_key_exists($schoolId, $languages)) {
            return $languages[$schoolId];
        }

        return $languages[$schoolId] = Cache::rememberForever('system_language_'.$schoolId, fn () => SmLanguage::where('school_id', $schoolId)->select('language_universal', 'native')->get());

    }
}

if (! function_exists('academicYears')) {
    function academicYears()
    {
        static $years = null;
        if ($years !== null) {
            return $years;
        }

        // session()->forget('academic_years');
        if (moduleStatusCheck('University')) {
            if (! session()->has('academic_years')) {
                $academic_years = Auth::check() ? UnAcademicYear::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get() : '';
                session()->put('academic_years', $academic_years);

                return $years = session()->get('academic_years');
            }

            return $years = session()->get('academic_years');

        }

        if (session()->has('academic_years')) {
            return $years = session()->get('academic_years');
        }

        $academic_years = Auth::check() ? SmAcademicYear::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get() : '';
        session()->put('academic_years', $academic_years);

        return $years = session()->get('academic_years');

    }
}

if (! function_exists('getActiveSubjects')) {
    function getActiveSubjects()
    {
        return SmSubject::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->get();
    }
}

if (! function_exists('subjectFullMark')) {
    function subjectFullMark($examtype, $subject, $class_id = null, $section_id = null, $shift_id = null)
    {
        $school_id = 1;
        if (Auth::check()) {
            $school_id = Auth::user()->school_id;
        } elseif (app()->bound('school')) {
            $school_id = app('school')->id;
        }

        try {
            $full_mark = SmExam::withOutGlobalScopes()
                ->where('school_id', $school_id)
                ->where('exam_type_id', $examtype);
            if (moduleStatusCheck('University')) {
                $full_mark = $full_mark->where('un_subject_id', $subject);

                return round(optional($full_mark->first(['exam_mark']))->exam_mark ?? 0, 2);
            }
            $full_mark = $full_mark->where('subject_id', $subject)
                ->where('class_id', $class_id);

            if ($section_id !== null) {
                $full_mark = $full_mark->where('section_id', $section_id);
            }
            if ($shift_id !== null) {
                $full_mark = $full_mark->where('shift_id', $shift_id);
            }

            return round(optional($full_mark->first(['exam_mark']))->exam_mark ?? 0, 2);

        } catch (Exception $exception) {
            return 0;
        }
    }
}

if (! function_exists('teacherAccess')) {
    function teacherAccess($user = null): bool
    {
        if (! $user) {
            $user = auth()->user();
        }

        return $user?->role_id === 4;

    }
}

if (! function_exists('getAllUserForChatBasedOnCondition')) {
    function getAllUserForChatBasedOnCondition()
    {
        try {
            $users = User::with('roles')->where('id', '!=', auth()->id())->get();
            if (app('general_settings')->get('chat_can_teacher_chat_with_parents') === 'no' && auth()->user()->roles->id === 4) {
                foreach ($users as $index => $user) {
                    if ($user->roles->id === 3) {
                        $users->forget($index);
                    }
                }
            }

            return $users;
        } catch (Throwable $throwable) {
            return false;
        }
    }
}

if (! function_exists('chatOpen')) {
    function chatOpen(): bool
    {
        return app('general_settings')->get('chat_open') === 'yes';
    }
}

// Jitsi Module Start
if (! function_exists('getDomainName')) {
    function getDomainName($url)
    {
        $url_domain = preg_replace('(^https?://)', '', $url);
        $url_domain = preg_replace('(^http?://)', '', $url_domain);

        return str_replace('/', '', $url_domain);
    }
}

// Jitsi Module End

if (! function_exists('invitationRequired')) {
    function invitationRequired(): bool
    {
        return app('general_settings')->get('chat_invitation_requirement') === 'required';
    }
}

if (! function_exists('intallMdouleMenu')) {
    function intallMdouleMenu($module_id, $module_name): bool
    {
        if (Auth::user()->role_id === 2 || Auth::user()->role_id === 3) {
            $menu_manage_module_id = MenuManage::where('active_status', 1)
                ->where('user_id', Auth::user()->id)
                ->where('role_id', Auth::user()->role_id)
                ->where('module_id', $module_id)
                ->first();
        } else {
            $menu_manage_module_id = MenuManage::where('active_status', 1)
                ->where('user_id', Auth::user()->id)
                ->where('role_id', Auth::user()->role_id)
                ->where('module_addons', $module_id)
                ->first();
        }

        return moduleStatusCheck($module_name) === true && is_null($menu_manage_module_id);

    }
}

if (! function_exists('customFieldValue')) {
    function customFieldValue($student_id, $labelName, $formName)
    {
        $custom_field_values = [];
        if ($formName === 'student_registration') {
            $custom_field_data = SmStudent::withOutGlobalScopes()->where('id', $student_id)->first();
            if (is_null($custom_field_data) && moduleStatusCheck('ParentRegistration')) {
                $custom_field_data = SmStudentRegistration::find($student_id);
            }

            @$value = $custom_field_data->custom_field;
        } elseif ($formName === 'staff_registration') {
            $custom_field_data = SmStaff::withOutGlobalScopes()->where('id', $student_id)->first();
            $value = $custom_field_data->custom_field;
        } elseif ($formName === 'school_registration') {
            $custom_field_data = SmSchool::withOutGlobalScopes()->where('id', $student_id)->first();
            $value = $custom_field_data->custom_field;
        } else {
            $value = null;
        }

        if ($value !== null) {
            $custom_field_values = json_decode($custom_field_data->custom_field, true) ?? [];
            $input_name = str_replace('-','_', \Illuminate\Support\Str::slug($labelName));
            if (array_key_exists($input_name, $custom_field_values)) {
                return $custom_field_values[$input_name];
            } elseif (array_key_exists($labelName, $custom_field_values)) {
                // Fallback for older data that might have been saved differently
                return $custom_field_values[$labelName];
            }

            return null;

        }

        return null;
    }
}

if (! function_exists('moduleVersion')) {
    function moduleVersion(string $module_name)
    {

        $dataPath = base_path('Modules/'.$module_name.'/' . $module_name . '.json');
        $strJsonFileContents = file_get_contents($dataPath);
        $array = json_decode($strJsonFileContents, true);
        return $array[$module_name]['versions'][0];
    }
}

if (! function_exists('menuPosition')) {
    function menuPosition($id)
    {

        $is_have = count(app('sidebar_news')) > 0;
        if ($id === 'is_submit') {
            return $is_have ? 1 : 0;
        }

        if ($is_have) {
            $sidebar = app('sidebar_news')->where('active_status', 1)->where('infix_module_id', $id)->first();

            return $sidebar ? $sidebar->parent_position_no : $id;
        }

        return false;

    }
}

if (! function_exists('menuStatus')) {
    function menuStatus($id)
    {
        $is_have = count(app('sidebar_news')) > 0;
        if (($is_have)) {
            $is_have_id = app('sidebar_news')->where('infix_module_id', $id)->first();
            if ($is_have_id) {
                return $is_have_id->active_status === 1;
            }

            // $id is a numeric Sidebar infix_module_id; userPermission() expects a route string.
            // Passing a numeric ID never matches, so we fall back to true and let route-level
            // guards (userPermission on the route) handle visibility instead.
            return true;

        }

        return true;
    }
}

if (! function_exists('courseSetting')) {
    function courseSetting()
    {
        return CourseSetting::where('school_id', Auth::user()->school_id)->first();
    }
}

if (! function_exists('frontCourseSetting')) {
    function frontCourseSetting()
    {
        return CourseSetting::where('school_id', app('school')->id)->first();
    }
}

if (! function_exists('fileUpload')) {
    /**
     * Backward-compatible wrapper — now delegates to FileUploadService.
     * All 80+ existing calls continue working without any changes.
     * FileUploadService handles dir creation, naming, and path return.
     */
    function fileUpload($file, string $destination): string
    {
        if (! $file) {
            return '';
        }

        return app(\App\Contracts\FileUploadServiceInterface::class)->upload($file, $destination);
    }
}

if (! function_exists('fileUpdate')) {
    /**
     * Backward-compatible wrapper — now delegates to FileUploadService.
     * All 72+ existing calls continue working without any changes.
     * Deletes old file, uploads new one, returns stored path.
     */
    function fileUpdate($databaseFile, $file, $destination): string
    {
        return app(\App\Contracts\FileUploadServiceInterface::class)->update($databaseFile, $file, $destination);
    }
}

if (! function_exists('uploadPath')) {
    /**
     * Resolve an upload directory key to its full path from config/uploads.php.
     *
     * USAGE:
     *   // Instead of hard-coding:
     *   $file->move('public/uploads/leave_request/', $fileName);
     *
     *   // Use the config key:
     *   fileUpload($file, uploadPath('leave_request'));
     *   // Returns: 'public/uploads/leave_request/'
     *
     * @param  string  $key  Key from config/uploads.php (e.g. 'student', 'leave_request')
     * @return string Full path e.g. 'public/uploads/leave_request/'
     */
    function uploadPath(string $key): string
    {
        return app(\App\Contracts\FileUploadServiceInterface::class)->resolvePath($key);
    }
}

if (! function_exists('putEnvConfigration')) {
    function putEnvConfigration(string $envKey, string $envValue): bool
    {

        $value = '"'.$envValue.'"';
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n";
        $keyPosition = mb_strpos($str, $envKey.'=');

        if (is_bool($keyPosition)) {

            $str .= $envKey.'="'.$envValue.'"';
        } else {
            $endOfLinePosition = mb_strpos($str, "\n", $keyPosition);
            $oldLine = mb_substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            $str = str_replace($oldLine, sprintf('%s=%s', $envKey, $value), $str);

            $str = mb_substr($str, 0, -1);
        }

        return (bool) file_put_contents($envFile, $str);

    }
}

// time format 2 hours 30 min
if (! function_exists('timeCalculation')) {
    function timeCalculation($time): string
    {
        $minutes = floor(($time / (60)) % 60);
        $hours = floor(($time / (60 * 60)));

        $hours = ($hours < 10) ? '0'.$hours : $hours;

        $minutes = ($minutes < 10) ? '0'.$minutes : $minutes;
        if ($hours === 0) {
            return $minutes.' minutes ';
        }

        return $hours.' hours '.$minutes.' minutes ';
    }
}

function spn_active_link($route_or_path, string $class = 'active')
{

    if (is_array($route_or_path)) {
        foreach ($route_or_path as $route) {
            if (request()->is($route)) {
                return $class.' a';
            }
        }

        return in_array(request()->route()->getName(), $route_or_path) ? $class : false;
    }

    if (request()->route()->getName() === $route_or_path) {
        return $class.' b';
    }

    if (request()->is($route_or_path)) {
        return $class.' c';
    }

    return false;
}

function spn_nav_item_open($data, $default_class = 'active')
{
    foreach ($data as $d) {
        if (spn_active_link($d, true)) {
            return $default_class;
        }
    }

    return false;
}

if (! function_exists('apk_secret')) {
    function apk_secret()
    {
        return Storage::exists('.apk_secret') ? Storage::get('.apk_secret') : false;
    }
}

function studentFieldLabel($fields, string $name)
{
    $field = $fields->where('field_name', $name)->first();
    if ($field && $field->label_name) {
        return $field->label_name;
    }

    return __('student.'.$name);
}

if (! function_exists('is_required')) {
    function is_required($field_name): bool
    {
        auth()->user()->school_id;
        $fields = getStudentRegistrationFields();
        $field = $fields->where('field_name', $field_name)
            ->first();

        return $field && $field->is_required === 1;
    }
}

if (! function_exists('is_show')) {
    function is_show($field_name): bool
    {
        $fields = getStudentRegistrationFields();
        $field = $fields->where('field_name', $field_name)->first();
        if (moduleStatusCheck('ParentRegistration')) {
            return $field && $field->is_show === 1;
        }

        return $field && $field->is_show === 1;

    }
}

if (! function_exists('getStudentRegistrationFields')) {
    function getStudentRegistrationFields($school_id = null)
    {

        if (! $school_id) {
            $school_id = auth()->user()->school_id;
        }

        return Cache::rememberForever('student_field_'.$school_id, function () use ($school_id) {
            return SmStudentRegistrationField::where('school_id', $school_id)->get()->filter(function ($field): bool {
                return ! $field->admin_section || isMenuAllowToShow($field->admin_section);
            });
        });
    }
}

if (! function_exists('has_permission')) {
    function has_permission($field_name): bool
    {

        $fields = getStudentRegistrationFields()
            ->when(auth()->user()->role_id === 2, function ($query): void {
                $query->where('student_edit', 1);
            })
            ->when(auth()->user()->role_id === 3, function ($query): void {
                $query->where('parent_edit', 1);
            })
            ->pluck('field_name')->toArray();

        return in_array($field_name, $fields);
    }
}

if (! function_exists('studentRecords')) {
    function studentRecords($request = null, $student_id = null, $school_id = null)
    {
        $builder = StudentRecord::query()->with('classes', 'studentDetail')->where('active_status', 1);
        if ($student_id !== null) {
            $builder->where('student_id', $student_id);
        }

        if ($school_id !== null) {
            $builder->where('school_id', $school_id);
        } else {
            $builder->where('school_id', auth()->user()->school_id);
        }

        if ($request !== null && ! moduleStatusCheck('University')) {
            $builder->when($request->class, function ($query) use ($request): void {
                $query->where('class_id', $request->class);
            })
                ->when($request->section, function ($query) use ($request): void {
                    $query->where('section_id', $request->section);
                });
        }

        if ($request !== null && moduleStatusCheck('University')) {
            $builder->when($request->un_session_id, function ($q) use ($request): void {
                $q->where('un_session_id', $request->un_session_id);
            })
                ->when($request->un_faculty_id, function ($q) use ($request): void {
                    $q->where('un_faculty_id', $request->un_faculty_id);
                })
                ->when($request->un_department_id, function ($q) use ($request): void {
                    $q->where('un_department_id', $request->un_department_id);
                })
                ->when($request->un_academic_id, function ($q) use ($request): void {
                    $q->where('un_academic_id', $request->un_academic_id);
                })
                ->when($request->un_semester_id, function ($q) use ($request): void {
                    $q->where('un_semester_id', $request->un_semester_id);
                })
                ->when($request->un_semester_label_id, function ($q) use ($request): void {
                    $q->where('un_semester_label_id', $request->un_semester_label_id);
                });
        }

        return $builder->when(moduleStatusCheck('University'), function ($q): void {
            $q->where('un_academic_id', getAcademicId());
        }, function ($query): void {
            $query->where('academic_id', getAcademicId());
        })->where('is_promote', 0);
    }
}

if (! function_exists('SchoolModuleStatus')) {
    function SchoolModuleStatus($schoolmodule): bool
    {
        return isModuleForSchool($schoolmodule);
    }
}

if (! function_exists('universityColumns')) {
    function universityColumns($table): void
    {
        $columns = [
            'un_sessions' => 'un_session_id',
            'un_faculties' => 'un_faculty_id',
            'un_departments' => 'un_department_id',
            'un_academic_years' => 'un_academic_id',
            'un_semesters' => 'un_semester_id',
            'un_semester_labels' => 'un_semester_label_id',
        ];
        foreach ($columns as $key => $column) {
            if (! Schema::hasColumn($table, $column)) {
                $table->unsignedBigInteger($column)->nullable();
            }

            if (Schema::hasTable($key)) {
                $table->foreign($column)->on($key)->references('id')->cascadeOnDelete();
            }
        }
    }
}

const WEEK_DAYS = [
    3 => 1,
    4 => 2,
    5 => 3,
    6 => 4,
    7 => 5,
    1 => 6,
    2 => 0,
];

const WEEK_DAYS_BY_NAME = [
    'Saturday' => 6,
    'Sunday' => 0,
    'Monday' => 1,
    'Tuesday' => 2,
    'Wednesday' => 3,
    'Thursday' => 4,
    'Friday' => 5,
];

const PERMITTED_MODULE = [
    // keep it all lower case.
    'lead', 'lms', 'university', 'alumni',
];

if (! function_exists('getSussSchools')) {
    function getSussSchools()
    {
        Cache::remember('saasSchools', 120, function (): void {
            $schools = SmSchool::get();
        });

        return Cache::get('saasSchools');
    }
}

if (! function_exists('calandarSettingByMenuName')) {
    function calandarSettingByMenuName($menu_name)
    {

        return Cache::rememberForever('calendarSetting_'.auth()->user()->school_id.'_'.$menu_name, function () use ($menu_name) {
            return SmCalendarSetting::where('menu_name', $menu_name)
                ->select('font_color', 'bg_color', 'status')
                ->first();
        });
    }
}
if (! function_exists('shifts')) {
    function shifts()
    {
        try {
            $shifts = branchWise(Shift::where('active_status', 1)
                ->where('school_id', auth()->user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get());

            return $shifts;
        } catch (Exception $e) {
            return [];
        }
    }
}

if (! function_exists('SaasSchool')) {
    function SaasSchool()
    {

        if (app()->bound('school')) {
            return app('school');
        }

        $request = request();
        $host = $request->getHttpHost();
        $school = null;
        $short_url = preg_replace('#^https?://#', '', rtrim(env('APP_URL', 'http://localhost'), '/'));

        $domain = str_replace('.'.$short_url, '', $host);

        if ($domain === $host) {
            $domain = null;
        }

        $saas_module = base_path('Modules/Saas/Providers/SaasServiceProvider.php');

        if (file_exists(filename: $saas_module)) {

            $module_status = json_decode(file_get_contents(base_path('modules_statuses.json')), true);

            if (isset($module_status['Saas']) && $module_status['Saas']) {

                if ($domain) {
                    $school = SmSchool::where(['domain' => $domain, 'active_status' => 1])->firstOrFail();

                } elseif ($host === $short_url) {
                    $school = SmSchool::where('id', 1)->first(); // \App\Models\SmSchool::findOrFail(1);
                } elseif ($host !== $short_url && config('app.allow_custom_domain')) {
                    $school = SmSchool::where('custom_domain', $host)->where('active_status', 1)->first(); // \App\Models\SmSchool::where(['custom_domain' => $host, 'active_status' => 1])->firstOrFail();
                } elseif (Auth::check()) {
                    $school = SmSchool::where('id', Auth::user()->school_id)->first();
                    SmSchool::findOrFail(Auth::user()->school_id);
                }
            }
        }
        if (! $school) {
            $school = Auth::check() ? auth()->user()->school : SmSchool::where('id', 1)->first();
        }
        app()->forgetInstance('school');
        app()->instance('school', $school);

        return $school;
    }
}

if (! function_exists('SaasDomain')) {
    function SaasDomain()
    {
        $school = SaasSchool();
        return $school ? $school->domain : '';
    }
}

if (! function_exists('saasEnv')) {
    function saasEnv($value, $default = null)
    {

        try {

            $domain = SaasDomain();
            $settings_prefix = Str::lower(str_replace(' ', '_', $domain));
            $path = storage_path('app/chat/'.$settings_prefix.'_settings.json');
            if (! file_exists($path)) {
                copy(storage_path('app/chat/default_settings.json'), $path);
            }

            $data = json_decode(file_get_contents($path), true);

            $settings = [];
            if (! empty($data)) {
                foreach ($data as $key => $property) {
                    $settings[$key] = $property;
                }
            }

            $env = $settings[$value] ?? '';
        } catch (Throwable $throwable) {
            $env = null;
        }

        if (empty($env)) {
            return $default;
        }

        return $env;
    }
}

function filterHeaderItems(array $items, string $keyword = 'header'): array
{
    $header = [];
    foreach ($items as $key => $item) {
        if (mb_stripos($key, $keyword.':') === 0) {
            unset($items[$key]);
            $key = str($key)->replace($keyword.':', '')->trim(' ')->value();
            $header[$key] = $item;
        }
    }

    return [
        'header' => $header,
        'body' => $items,
    ];
}

// if (! function_exists('total_no_records')) {
//     function total_no_records($class_id, $section_id = null)
//     {
//         try {
//             $records = StudentRecord::query();
//             $records->where('class_id', $class_id)->where('is_promote', 0);
//             if ($section_id) {
//                 $records->where('section_id', $section_id);
//             }

//             return $records->whereHas('student')->count();
//         } catch (Throwable $throwable) {
//             return 0;
//         }
//     }
// }

function total_no_records($class_id, $section_id = null, $shift_id = null)
{
    $query = StudentRecord::query();

    $query->where('class_id', $class_id);

    if ($section_id) {
        $query->where('section_id', $section_id);
    }

    if ($shift_id) {
        $query->where('shift_id', $shift_id);
    }

    return $query->count();
}

if (! function_exists('isSkip')) {
    function isSkip($name): bool
    {
        $data = \App\Models\ExamStepSkip::where('name', $name)->where('school_id', auth()->user()->school_id)->first();

        return (bool) $data;
    }
}

if (! function_exists('UngetStudentMeritPosition')) {
    function UngetStudentMeritPosition($un_academic_id, $un_semester_label_id, $exam_term_id, $record_id, $un_section_id, $un_faculty_id, $un_department_id)
    {
        try {
            $position = ExamMeritPosition::withOutGlobalScopes()->where('un_academic_id', $un_academic_id)
                ->where('un_semester_label_id', $un_semester_label_id)

                ->where('exam_term_id', $exam_term_id)
                ->where('record_id', $record_id)
                ->where('un_section_id', $un_section_id)
                ->where('un_faculty_id', $un_faculty_id)
                ->where('un_department_id', $un_department_id)
                ->first();
            if ($position) {
                return $position->position;
            }

            return '';

        } catch (Throwable $th) {
            return false;
        }
    }
}

if (! function_exists('getStudentAllExamMeritPosition')) {
    function getStudentAllExamMeritPosition($class_id, $section_id, $record_id)
    {
        try {
            $position = AllExamWisePosition::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('record_id', $record_id)
                ->first();
            if ($position) {
                return $position->position;
            }

            return null;

        } catch (Throwable $throwable) {
            return false;
        }
    }
}

if (! function_exists('db_engine')) {
    function db_engine()
    {
        try {
            return \DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (Exception $exception) {
            return 'mysql';
        }
    }
}

if (! function_exists('inAppLiveClassJoinAndClose')) {
    function inAppLiveClassJoinAndClose($classMeeting): bool
    {
        $currentDayStatus = Carbon::now();
        $currentTime = $currentDayStatus->format('g:i A');
        $givenTime = Carbon::parse($classMeeting->time)->addMinutes((int) $classMeeting->duration)->format('g:i A');
        if ($currentDayStatus->format('Y-m-d') <= Carbon::parse($classMeeting->date)->format('Y-m-d') && is_null($classMeeting->end_at)) {
            if ($currentTime >= Carbon::parse($classMeeting->time)->format('g:i A')) {
                return $currentDayStatus->isBetween($currentTime, $givenTime);
            }

            return false;

        }

        return false;

    }
}

if (! function_exists('storeCalendarInfo')) {
    function storeCalendarInfo(
        $title,
        $description,
        $date,
        $created_by,
        $record_id = null,
        $role_id = null,
        $staff_id = null,
        $parent_id = null,
        $class_id = null,
        $section_id = null,
        $url = null

    ): void {
        $storeData = new SmCalendar();
        $storeData->record_id = $record_id;
        $storeData->role_id = $role_id;
        $storeData->staff_id = $staff_id;
        $storeData->parent_id = $parent_id;
        $storeData->class_id = $class_id;
        $storeData->section_id = $section_id;
        $storeData->title = $title;
        $storeData->description = $description;
        $storeData->url = $url;
        $storeData->date = $date;
        $storeData->created_by = $created_by;
        $storeData->school_id = auth()->user()->school_id;
        $storeData->academic_id = getAcademicId();
        $storeData->save();
    }
}

if (! function_exists('getYoutubeName')) {
    function getYoutubeName($link): string
    {
        return explode('</title>', explode('<title>', file_get_contents($link))[1])[0];
    }
}

if (! function_exists('youtubeVideoLinkValidation')) {
    function youtubeVideoLinkValidation($link)
    {
        return preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]{11})/", $link);
    }
}

if (! function_exists('generateRandomString')) {
    function generateRandomString($length): string
    {
        $validChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';
        $validCharsLen = mb_strlen($validChars);
        $str = '';
        $i = 0;
        while ($i++ < $length) {
            $str .= $validChars[random_int(0, $validCharsLen - 1)];
        }

        return $str;
    }
}

if (! function_exists('activeTheme')) {
    function activeTheme()
    {
        try {
            return generalSetting()->active_theme;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (! function_exists('getPermissions')) {
    function getPermissions()
    {
        Cache::rememberForever('permissions', function (): void {
            Permission::with(['subModule'])->get();
        });

        return Cache::get('permissions');
    }
}

if (! function_exists('userLocal')) {
    function userLocal()
    {
        try {
            $user = auth()->user();

            return $user->language ?? App::getLocale();
        } catch (Throwable $throwable) {
            return 'en';
        }
    }
}

if (! function_exists('_translation')) {
    function _translation($key)
    {
        $trans = trans($key);
        try {
            $exp = explode('.', $trans);
            if (count($exp) === 2) {
                $txt = Str::replace('_', ' ', ucfirst($exp[1]));

                return ucfirst($txt);
            }

            $txt = $trans;
            $txt = Str::replace('_', ' ', ucfirst($txt));

            return ucfirst($txt);
        } catch (Throwable $throwable) {
            return $key;
        }
    }
}

if (! function_exists('_trans')) {
    function _trans($value)
    {

        try {
            if (env('APP_ENV') === 'production') {
                return trans($value);
            }

            $local = userLocal() ?: app()->getLocale();

            $langPath = resource_path('lang/'.$local.'/');

            if (! file_exists($langPath)) {
                mkdir($langPath, 0777, true);
            }

            if (str_contains($value, '.')) {
                $new_trns = explode('.', $value);
                $file_name = $new_trns[0];
                // $trans_key = $new_trns[1];
                $trans_key = str_replace($file_name.'.', '', $value);

                $file_path = $langPath.''.$file_name.'.php';
                if (file_exists($file_path)) {
                    $file_content = include $file_path;

                    if (array_key_exists($trans_key, $file_content)) {
                        return _translation($value);
                    }

                    $file_content[$trans_key] = $trans_key;
                    $str = <<<'EOT'
                                            <?php
                                                return [
                                            EOT;
                    foreach ($file_content as $key => $val) {
                        if (gettype($val) === 'string') {

                            $line = <<<EOT
                                                                    "{$key}" => "{$val}",\n
                                                                EOT;
                        }

                        if (gettype($val) === 'array') {
                            $line = <<<EOT
                                                                            "{$key}" => [\n
                                                                        EOT;
                            $str .= $line;
                            foreach ($val as $lang_key => $lang_val) {

                                $line = <<<EOT
                                                                            "{$lang_key}" => "{$lang_val}",\n
                                                                        EOT;

                                $str .= $line;
                            }

                            $line = <<<EOT
                                                                        ],\n
                                                                    EOT;
                        }

                        $str .= $line;
                    }

                    $end = <<<'EOT'
                                                    ]
                                            ?>
                                            EOT;
                    $str .= $end;

                    file_put_contents($file_path, $str, $flags = 0, $context = null);

                } else {

                    fopen($file_path, 'w');
                    $file_content = [];
                    $file_content[$trans_key] = $trans_key;
                    $str = <<<'EOT'
                                            <?php
                                                return [
                                            EOT;
                    foreach ($file_content as $key => $val) {
                        if (gettype($val) === 'string') {

                            $line = <<<EOT
                                                                    "{$key}" => "{$val}",\n
                                                                EOT;
                        }

                        if (gettype($val) === 'array') {
                            $line = <<<EOT
                                                                            "{$key}" => [\n
                                                                        EOT;
                            $str .= $line;
                            foreach ($val as $lang_key => $lang_val) {

                                $line = <<<EOT
                                                                            "{$lang_key}" => "{$lang_val}",\n
                                                                        EOT;

                                $str .= $line;
                            }

                            $line = <<<EOT
                                                                        ],\n
                                                                    EOT;
                        }

                        $str .= $line;
                    }

                    $end = <<<'EOT'
                                                    ]
                                            ?>
                                            EOT;
                    $str .= $end;

                    file_put_contents($file_path, $str, $flags = 0, $context = null);
                }

                return _translation($value);
            }

            $trans_key = $value;
            $file_path = resource_path('lang/'.$local.'/'.$local.'.php');

            fopen($file_path, 'w');
            $file_content = [];
            $file_content[$trans_key] = $trans_key;
            $str = <<<'EOT'
                                            <?php
                                                return [
                                            EOT;
            foreach ($file_content as $key => $val) {
                if (gettype($val) === 'string') {

                    $line = <<<EOT
                                                                    "{$key}" => "{$val}",\n
                                                                EOT;
                }

                if (gettype($val) === 'array') {
                    $line = <<<EOT
                                                                            "{$key}" => [\n
                                                                        EOT;
                    $str .= $line;
                    foreach ($val as $lang_key => $lang_val) {

                        $line = <<<EOT
                                                                            "{$lang_key}" => "{$lang_val}",\n
                                                                        EOT;

                        $str .= $line;
                    }

                    $line = <<<EOT
                                                                        ],\n
                                                                    EOT;
                }

                $str .= $line;
            }

            $end = <<<'EOT'
                                                    ]
                                            ?>
                                            EOT;
            $str .= $end;

            file_put_contents($file_path, $str, $flags = 0, $context = null);

            return _translation($value);

        } catch (Exception $exception) {
            return $value;
        }
    }
}

if (! function_exists('defaultLogo')) {
    function defaultLogo($path)
    {
        if ($path && file_exists($path)) {
            return assetPath($path);
        }

        return assetPath('public/uploads/settings/logo.png');

    }
}

if (! function_exists('defaultUserLogo')) {
    function defaultUserLogo($path)
    {
        if ($path && file_exists($path)) {
            return assetPath($path);
        }

        return assetPath('public/uploads/staff/demo/staff.jpg');

    }
}

if (! function_exists('latterAvater')) {
    function latterAvater($string): string
    {
        $words = explode(' ', $string);
        if (count($words) > 1) {
            return mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1);
        }

        $first = mb_substr($string, 0, 1);
        $last = mb_substr($string, -1);

        return $first.$last;
    }
}

if (! function_exists('getProfileImage')) {
    function getProfileImage($user_id)
    {
        $user = User::find($user_id);
        $role_id = $user->role_id;
        $student = SmStudent::where('user_id', $user_id)->first();
        $parent = SmParent::where('user_id', $user_id)->first();
        $staff = SmStaff::where('user_id', $user_id)->first();
        if ($role_id === 2) {
            $profile = $student->student_photo ?: 'public/backEnd/assets/img/avatar.png';
        } elseif ($role_id === 3) {
            $profile = $parent->fathers_photo ?: 'c';
        } else {
            $profile = $staff->staff_photo ?: 'public/backEnd/assets/img/avatar.png';
        }

        return $profile;
    }

    function headerContent()
    {
        $headerPageData = Page::where('school_id', app('school')->id)->where('name', 'header_menu')
            ->select('id', 'name', 'title', 'description', 'slug', 'settings', 'status')
            ->first();
        if ($headerPageData) {
            return view('pagebuilder::components.header-footer-page-components', ['page' => $headerPageData]);
        }

        return null;
    }

    function footerContent()
    {
        $footerPage = Page::where('school_id', app('school')->id)->where('name', 'footer_menu')
            ->select('id', 'name', 'title', 'description', 'slug', 'settings', 'status')
            ->first();
        if ($footerPage) {
            return view('pagebuilder::components.header-footer-page-components', ['page' => $footerPage]);
        }

        return null;
    }

    function formatedDate($date): string
    {
        return date('Y-m-d', strtotime($date));
    }
}

if (! function_exists('envu')) {
    function envu($data = []): bool
    {
        foreach ($data as $key => $value) {
            if (env($key) === $value) {
                unset($data[$key]);
            }
        }

        if (count($data) === 0) {
            return false;
        }

        $env = file_get_contents(base_path().'/.env');
        $env = explode("\n", $env);
        foreach ((array) $data as $key => $value) {
            foreach ($env as $env_key => $env_value) {
                $entry = explode('=', $env_value, 2);
                if ($entry[0] === $key) {
                    $env[$env_key] = $key.'='.(is_string($value) ? '"'.$value.'"' : $value);
                } else {
                    $env[$env_key] = $env_value;
                }
            }
        }

        $env = implode("\n", $env);
        file_put_contents(base_path().'/.env', $env);

        return true;
    }
}

if (! function_exists('lastOneMonthDates')) {
    /**
     * @return string[]
     */
    function lastOneMonthDates(): array
    {
        $days_ago = [];
        for ($i = 30; $i >= 1; $i--) {
            $day = date('Y-m-d', strtotime('-'.$i.' days', strtotime(date('Y-m-d'))));
            $days_ago[] = $day;
        }

        return $days_ago;
    }
}

if (! function_exists('insertMenuManage')) {
    function insertMenuManage($menu): void
    {
        $menuData = SmHeaderMenuManager::create($menu);
        if (gv($menu, 'childs')) {
            foreach (gv($menu, 'childs') as $child) {
                $child['parent_id'] = $menuData->id;
                insertMenuManage($child);
            }
        }
    }
}

// if (!function_exists('asset_path')) {
//     function asset_path($path = null)
//     {
//         return 'public/' . $path;
//     }
// }

if (! function_exists('forumSetting')) {
    function forumSetting()
    {
        return ForumSetting::where('school_id', 1)->withoutGlobalScopes()->first();
    }
}

if (! function_exists('get_logo')) {
    function get_logo()
    {
        $generalSetting = generalSetting();
        $logoPath = $generalSetting->logo;

        if (! empty($logoPath) && file_exists(public_path($logoPath))) {
            return assetPath($logoPath);
        }

        return assetPath('public/uploads/settings/logo.png');

    }
}

if (! function_exists('generateQRCode')) {
    function generateQRCode(string $text)
    {

        try {
            if (! file_exists(public_path('qr_codes/'.$text.'-qrcode.png'))) {
                $qr_renderer = new ImageRenderer(
                    new RendererStyle(400, 1),
                    new ImagickImageBackEnd()
                );
                $writer = new Writer($qr_renderer);
                $writer->writeFile($text, public_path('qr_codes/'.$text.'-qrcode.png'));

                return assetPath('public/qr_codes/'.$text.'-qrcode.png');
            }
        } catch (Exception $exception) {
            Log::error('Error on QR code Generate '.$exception->getMessage());
        }

        return null;
    }
}

if (! function_exists('qrAttendanceSetting')) {
    function qrAttendanceSetting($class, $section, $subject = null)
    {
        $setting = new QRCodeAttendanceSetting();
        $setting = $setting->where('class_id', $class)
            ->where('section_id', $section)
            ->where('school_id', auth()->user()->school_id);
        if ($subject) {
            $setting = $setting->where('subject_id', $subject);
        }

        return $setting->first();

    }
}

if (! function_exists('dayNames')) {
    function dayNames(): array
    {

        return [
            'Saturday' => 'Friday',
            'Sunday' => 'Saturday',
            'Monday' => 'Sunday',
            'Tuesday' => 'Monday',
            'Wednesday' => 'Tuesday',
            'Thursday' => 'Wednesday',
            'Friday' => 'Thursday',
        ];
    }
}

if (! function_exists('getWeekendDay')) {
    function getWeekendDay($day)
    {
        return dayNames()[$day];
    }
}

if (! function_exists('getWeekNumber')) {
    function getWeekNumber($date): string
    {
        // Convert the date to a Unix timestamp
        $timestamp = strtotime($date);

        // Get the ISO-8601 week number
        $weekNumber = date('W', $timestamp);

        return $weekNumber;
    }
}

if (! function_exists('toastrError')) {
    function toastrError($message = 'Operation Failed', $title = 'Failed'): void
    {
        $toastr = app(Brian2694\Toastr\Toastr::class);
        $toastr->error($message, $title);
    }
}

if (! function_exists('toastrSuccess')) {
    function toastrSuccess($message = 'Operation Success', $title = 'Success'): void
    {
        $toastr = app(Brian2694\Toastr\Toastr::class);
        $toastr->success($message, $title);
    }
}

if (! function_exists('toastrWarning')) {
    function toastrWarning($message = 'Operation Warning', $title = 'Warning'): void
    {
        $toastr = app(Brian2694\Toastr\Toastr::class);
        $toastr->warning($message, $title);
    }
}

if (! function_exists('ad')) {
    function ad(mixed ...$vars): void
    {
        if (config('app.debug')) {
            foreach ($vars as $key => $value) {
                Log::info(is_int($key) ? 'Variable '.$key : $key, [
                    'dump' => print_r($value, true),
                ]);
            }

            dd(...$vars);
        }
    }
}

// if (! function_exists('showTimelineDocName')) {
// function showTimelineDocName($data)
// {
//     $name = explode('/', $data);
//     $number = count($name);
//     return $name[$number - 1];
// }
// }

// if (! function_exists('showDocumentName')) {
//     function showDocumentName($data)
//     {
//         $name = explode('/', $data);
//         $number = count($name);
//         return $name[$number - 1];
//     }
// }

if (! function_exists('validationMessage')) {
    function validationMessage(array $rules): array
    {
        return [
            'file.required' => 'Please upload a file.',
            'file.mimes' => 'Only CSV, XLS, or XLSX files are allowed.',
            'file.max' => 'File size must not exceed 2MB.',
            'index.required' => 'Index mapping is required.',
            'index.array' => 'Index must be an array.',
        ];
    }
}

if (! function_exists('shiftEnable')) {
    function shiftEnable(): bool
    {
        try {
            $generalSetting = generalSetting();

            return isset($generalSetting->shift_enable) && (bool) $generalSetting->shift_enable;
        } catch (Throwable $e) {
            return false;
        }
    }
}

if (! function_exists('convertToSnakeCase')) {
    function convertToSnakeCase($string)
    {
        return Str::snake($string);
    }
}

if (! function_exists('convertToTitleCase')) {
    function convertToTitleCase($string)
    {
        return Str::title(str_replace('_', ' ', $string));
    }
}

if (! function_exists('getWeekendDays')) {
    function getWeekendDays($schoolId)
    {
        return \App\Models\SmWeekend::where('school_id', Auth::user()->school_id)
            ->where('is_weekend', 1)
            ->where('active_status', 1)
            ->pluck('name')
            ->toArray();
    }
}

if (! function_exists('getAllExamPosition')) {
    function getAllExamPosition($record_id, $class_id, $section_id)
    {
        $position = AllExamWisePosition::where('record_id', $record_id)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('school_id', auth()->user()->school_id)
            ->where('academic_id', getAcademicId())
            ->first();

        return $position->position ?? '';
    }
}

if (! function_exists('userBranch')) {
    function userBranch()
    {
        if (session()->has('user_branch')) {
            return session()->get('user_branch');
        }

        $branch_id = Auth::user()->branch_id ?? '';
        
        // If no branch is assigned and Branch module is active, get the first branch
        if (! $branch_id && function_exists('moduleStatusCheck') && moduleStatusCheck('Branch')) {
            $firstBranch = Branch::where('status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('id')
                ->first(['id']);
            
            if ($firstBranch) {
                $branch_id = $firstBranch->id;
            }
        }
        
        session()->put('user_branch', $branch_id);

        return session()->get('user_branch');
    }
}

if (! function_exists('getBranch')) {
    function getBranch(): Illuminate\Support\Collection
    {
        static $memo = null;
        if ($memo !== null) {
            return $memo;
        }

        try {
            if (! function_exists('moduleStatusCheck') || ! moduleStatusCheck('Branch')) {
                return $memo = collect();
            }

            $user = auth()->user();
            if (! $user) {
                return $memo = collect();
            }

            $schoolId = $user->school_id;
            $roleId = $user->role_id;
            $branchId = userBranch();

            // Generation version — bumped by clearBranchCache() on any write.
            $version = Cache::get('branches_v_'.$schoolId, 1);

            $cacheKey = 'branches_school_'.$schoolId
                .'_role_'.$roleId
                .'_branch_'.($branchId ?: 'all')
                .'_v'.$version;

            $memo = Cache::remember($cacheKey, 1800, function () use ($user, $roleId, $branchId, $schoolId) {
                $query = Branch::where('status', 1)
                    ->where('school_id', $schoolId)
                    ->orderBy('branch_name');

                if ($roleId !== 1) {
                    $query->where('id', $user->branch_id);
                } elseif ($branchId && $branchId !== 0) {
                    $query->where('id', $branchId);
                }

                return $query->get();
            });

            return $memo;

        } catch (Throwable $e) {
            return $memo = collect();
        }
    }
}

if (! function_exists('clearBranchCache')) {
    /**
     * Invalidate all getBranch() cache entries for a school in one operation.
     *
     * Call this wherever branch data changes:
     *
     *   // In your Branch controller:
     *   public function store(Request $request)   { ... clearBranchCache(auth()->user()->school_id); }
     *   public function update(Request $request)  { ... clearBranchCache(auth()->user()->school_id); }
     *   public function destroy($id)              { ... clearBranchCache(auth()->user()->school_id); }
     *
     *   // OR in a Branch Eloquent Observer (preferred — automatic, never forgotten):
     *   class BranchObserver {
     *       public function saved(Branch $branch)   { clearBranchCache($branch->school_id); }
     *       public function deleted(Branch $branch) { clearBranchCache($branch->school_id); }
     *   }
     *   // Register in AppServiceProvider::boot():
     *   Branch::observe(BranchObserver::class);
     *
     * How it works:
     *   Increments 'branches_v_{schoolId}'. getBranch() embeds this version in
     *   its cache key, so the next call gets a cache miss and re-queries the DB.
     *   Old versioned keys expire naturally after their 30-min TTL.
     */
    function clearBranchCache(int $schoolId): void
    {
        $versionKey = 'branches_v_'.$schoolId;
        $current = Cache::get($versionKey, 1);
        Cache::put($versionKey, $current + 1, now()->addDays(30));
    }
}

if (! function_exists('branchWise')) {
    function branchWise($query)
    {
        if (! function_exists('moduleStatusCheck') || ! moduleStatusCheck('Branch')) {
            return $query;
        }

        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        $branchId = branchWiseSelectedBranchId($user);
        if (! $branchId) {
            return $query;
        }

        return branchWiseApplyFilter($query, 'branch_id', $branchId);
    }
}

if (! function_exists('branchWiseBook')) {
    function branchWiseBook($query)
    {
        if (! function_exists('moduleStatusCheck') || ! moduleStatusCheck('Branch')) {
            return $query;
        }

        $user = auth()->user();
        if (! $user) {
            return $query;
        }

        $branchId = branchWiseSelectedBranchId($user);
        if (! $branchId) {
            return $query;
        }

        return branchWiseApplyFilter($query, 'sm_books.branch_id', $branchId, 'sm_books');
    }
}

if (! function_exists('branchWiseSelectedBranchId')) {
    function branchWiseSelectedBranchId($user)
    {
        $branchId = userBranch();

        if ($branchId && (string) $branchId !== '0') {
            return $branchId;
        }

        if ((int) $user->role_id !== 1 && $user->branch_id && (string) $user->branch_id !== '0') {
            return $user->branch_id;
        }

        return null;
    }
}

if (! function_exists('branchWiseApplyFilter')) {
    function branchWiseApplyFilter($query, string $column, $branchId, ?string $table = null)
    {
        if ($query instanceof Illuminate\Support\Collection) {
            $attribute = Str::after($column, '.');

            return $query->filter(function ($item) use ($attribute, $branchId) {
                $value = data_get($item, $attribute);

                return blank($value) || (string) $value === '0' || (string) $value === (string) $branchId;
            })->values();
        }

        if (! branchWiseHasColumn($query, $column, $table)) {
            return $query;
        }

        return $query->where(function ($query) use ($column, $branchId) {
            $query->where($column, $branchId)
                ->orWhereNull($column)
                ->orWhere($column, 0);
        });
    }
}

if (! function_exists('branchWiseHasColumn')) {
    function branchWiseHasColumn($query, string $column, ?string $table = null): bool
    {
        static $cache = [];

        $table = $table ?: branchWiseTableName($query);
        $column = Str::after($column, '.');

        if (! $table) {
            return true;
        }

        $key = $table.'.'.$column;
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        return $cache[$key] = Schema::hasColumn($table, $column);
    }
}

if (! function_exists('branchWiseTableName')) {
    function branchWiseTableName($query): ?string
    {
        if ($query instanceof Illuminate\Database\Eloquent\Builder) {
            return $query->getModel()->getTable();
        }

        if ($query instanceof Illuminate\Database\Query\Builder && is_string($query->from)) {
            $table = preg_split('/\s+as\s+/i', $query->from)[0] ?? $query->from;

            return trim($table, '` ');
        }

        return null;
    }
}

if (! function_exists('registrationSetting')) {
    /**
     * Cached ParentRegistration module settings for the current school.
     * Returns null when the ParentRegistration module is not installed.
     *
     * Replaces raw Modules\ParentRegistration\Entities\SmRegistrationSetting::where()
     * calls scattered across 3+ blade views — each was an independent DB query.
     * Now cached 30 minutes per school, shared across desktop menu, mobile menu,
     * page builder header, and front_master layout.
     */
    function registrationSetting(): ?Illuminate\Database\Eloquent\Model
    {
        static $cache = [];

        $schoolId = app()->bound('school') ? app('school')->id : 1;

        if (array_key_exists($schoolId, $cache)) {
            return $cache[$schoolId];
        }

        if (! moduleStatusCheck('ParentRegistration')) {
            $cache[$schoolId] = null;

            return null;
        }

        $cache[$schoolId] = Cache::remember(
            'registration_setting_'.$schoolId,
            1800,
            fn () => Modules\ParentRegistration\Entities\SmRegistrationSetting::where('school_id', $schoolId)->first()
        );

        return $cache[$schoolId];
    }
}

if (! function_exists('getAllBranches')) {
    /**
     * Get all active branches for the current school without role-based restrictions.
     * 
     * Returns a collection of all active branches regardless of user role or permissions.
     * Useful for admin views, reports, or dropdowns that need to display all branches.
     * 
     * @return Illuminate\Support\Collection
     */
    function getAllBranches(): Illuminate\Support\Collection
    {
        static $memo = null;
        if ($memo !== null) {
            return $memo;
        }

        try {
            if (! function_exists('moduleStatusCheck') || ! moduleStatusCheck('Branch')) {
                return $memo = collect();
            }

            $user = auth()->user();
            if (! $user) {
                return $memo = collect();
            }

            $schoolId = $user->school_id;

            // Version key for cache invalidation
            $version = Cache::get('branches_v_'.$schoolId, 1);
            $cacheKey = 'all_branches_school_'.$schoolId.'_v'.$version;

            $memo = Cache::remember($cacheKey, 1800, function () use ($schoolId) {
                return Branch::where('status', 1)
                    ->where('school_id', $schoolId)
                    ->orderBy('branch_name')
                    ->get();
            });

            return $memo;

        } catch (Throwable $e) {
            return $memo = collect();
        }
    }
}
