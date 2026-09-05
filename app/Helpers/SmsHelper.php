<?php

/**
 * SmsHelper.php — SMS gateway & push notification helpers.
 *
 * Extracted from Helper.php (P4 DRY cleanup).
 * Must be added to composer.json autoload.files to load globally.
 *
 * Contains: SMS gateway detection, send_sms, send_custom_sms,
 * sendNotification, send_notification, short code resolution.
 */

use AfricasTalking\SDK\AfricasTalking;
use App\Models\CustomSmsSetting;
use App\Models\SmNotification;
use App\Models\SmNotificationSetting;
use App\Models\SmSmsGateway;
use App\Models\SmsTemplate;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

if (! function_exists('activeSmsGateway')) {
    function activeSmsGateway()
    {
        $school_id = Auth::check() && saasSettings('sms_settings') ? Auth::user()->school_id : 1;

        return SmSmsGateway::where('school_id', $school_id)->where('active_status', '=', 1)->first();
    }
}

if (! function_exists('get_mobile_sms_data')) {
    function get_mobile_sms_data()
    {
        $school_id = Auth::check() && saasSettings('sms_settings') ? Auth::user()->school_id : 1;

        return SmSmsGateway::where('active_status', 1)->where('gateway_name', 'Mobile SMS')->where('school_id', $school_id)->first();
    }
}

if (! function_exists('sendNotification')) {
    function sendNotification($message, $url = null, $user_id = null, $role_id = null)
    {
        $notification = new SmNotification;
        $notification->date = date('Y-m-d');
        $notification->message = $message;
        $notification->url = $url;
        $notification->user_id = $user_id;
        $notification->role_id = $role_id;
        $notification->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University')) {
            $notification->un_academic_id = getAcademicId();
        } else {
            $notification->academic_id = getAcademicId();
        }

        return $notification->save();

    }
}

if (! function_exists('send_custom_sms')) {
    function send_custom_sms($reciver_number, $message, $active_gateway = null)
    {
        if (! $active_gateway) {
            $school_id = Auth::check() && saasSettings('sms_settings') ? Auth::user()->school_id : 1;
            $active_gateway = SmSmsGateway::where('active_status', 1)->where('school_id', $school_id)->first();
        }

        if (! $active_gateway) {
            return null;
        }

        $sms_settings = CustomSmsSetting::where('gateway_id', $active_gateway->id)->first();

        $response = false;
        if (empty($sms_settings->gateway_url)) {
            Toastr::info(__('common.set_sms_credentials'), __('common.info'));

            return $response;
        }

        $request_data = [
            $sms_settings->send_to_parameter_name => $reciver_number,
            $sms_settings->messege_to_parameter_name => $message,
        ];

        if (! empty($sms_settings->param_key_1)) {
            $request_data[$sms_settings->param_key_1] = $sms_settings->param_value_1;
        }

        if (! empty($sms_settings->param_key_2)) {
            $request_data[$sms_settings->param_key_2] = $sms_settings->param_value_2;
        }

        if (! empty($sms_settings->param_key_3)) {
            $request_data[$sms_settings->param_key_3] = $sms_settings->param_value_3;
        }

        if (! empty($sms_settings->param_key_4)) {
            $request_data[$sms_settings->param_key_4] = $sms_settings->param_value_4;
        }

        if (! empty($sms_settings->param_key_5)) {
            $request_data[$sms_settings->param_key_5] = $sms_settings->param_value_5;
        }

        if (! empty($sms_settings->param_key_6)) {
            $request_data[$sms_settings->param_key_6] = $sms_settings->param_value_6;
        }

        if (! empty($sms_settings->param_key_7)) {
            $request_data[$sms_settings->param_key_7] = $sms_settings->param_value_7;
        }

        if (! empty($sms_settings->param_key_8)) {
            $request_data[$sms_settings->param_key_8] = $sms_settings->param_value_8;
        }

        $params = [];

        $formatted = filterHeaderItems($request_data);
        $params['headers'] = gv($formatted, 'header', []);
        $request_data = gv($formatted, 'body', []);

        $user_name = array_search('username', $sms_settings->toArray(), true);
        $password = array_search('password', $sms_settings->toArray(), true);
        $authorization = array_search('authorization', $sms_settings->toArray(), true);

        if ($sms_settings->set_auth === 'header') {
            if ($user_name && $password) {
                $params['auth'] = [
                    $request_data[$sms_settings->$user_name],
                    $request_data[$sms_settings->$password],
                ];
                unset($request_data['username']);
                unset($request_data['password']);
            }
            if ($authorization) {
                $params['headers'] = [
                    'authorization' => $request_data[$sms_settings->$user_name],
                ];
            }

        }

        if (array_key_exists('csms_id', $request_data)) {
            $request_data->csms_id = date('dmY');
        }

        $params['form_params'] = $request_data;

        $client = new Client();
        $method = mb_strtolower($sms_settings->request_method);

        if ($method === 'get') {
            return $client->$method($sms_settings->gateway_url.'?'.http_build_query($request_data));
        }

        return $client->post($sms_settings->gateway_url, $params);
    }
}

if (! function_exists('send_notification')) {
    function send_notification($event, $user_id, $data)
    {
        $notification = SmNotificationSetting::where('event', $event)->where('school_id', auth()->user()->school_id)->first();
        $user = User::find($user_id);
        $all_recivers = $notification->recipient;
        $reciver = '';
        $active_recivers = [];
        $active_dest = [];
        $body = '';
        if ($user->role_id === 1) {
            $reciver = 'Admin';
        } elseif ($user->role_id === 2) {
            $reciver = 'Student';
        } elseif ($user->role_id === 3) {
            $reciver = 'Parent';
        } elseif ($user->role_id === 4) {
            $reciver = 'Teacher';
        }

        foreach ($all_recivers as $key => $value) {
            if ($value === 1) {
                $active_recivers[] = $key;
            }
        }

        if (in_array($reciver, $active_recivers)) {
            $destinations = $notification->destination;

            foreach ($destinations as $via => $value) {
                if ($value === 1) {
                    $active_dest[] = $via;
                }
            }

            if (in_array('Email', $active_dest)) {
                $body = short_code_messege($notification->template[$reciver]['Email'], $data);
                $view = view('backEnd.email.emailBody', ['body' => $body]);
                $message = (string) $view;
                $headers = "From: <$user->email> \r\n";
                $headers .= "Reply-To: $user->full_name <$user->email> \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8\r\n";
                @mail($user->email, $event, $message, $headers);
            }

            if (in_array('SMS', $active_dest)) {
                $sms_body = short_code_messege($notification->template[$reciver]['SMS'], $data);
            }

            if (in_array('Web', $active_dest)) {
                $web_body = short_code_messege($notification->template[$reciver]['Web'], $data);

                $notification = new SmNotification;
                $notification->user_id = $user->id;
                $notification->role_id = $user->role_id;
                $notification->school_id = $user->school_id;
                $notification->academic_id = getAcademicId();
                $notification->date = date('Y-m-d');
                $notification->message = $web_body;
                $notification->save();
            }

            if (in_array('App', $active_dest)) {
            }
        }

        return SmExamSignature::where('active_status', 1)->get(['title', 'signature']);
    }
}

if (! function_exists('send_sms')) {
    function send_sms(?string $reciver_number, $purpose, $data): void
    {
        if (! $reciver_number) {
            return;
        }
        if ($purpose !== 'test_sms') {
            $templete = getTempleteDetails($purpose, 'sms');
            if (! $templete) {
                return;
            }
        }

        $school_id = Auth::check() && saasSettings('sms_settings') ? Auth::user()->school_id : 1;

        $activeSmsGateway = SmSmsGateway::where('school_id', $school_id)->where('active_status', 1)->first();
        if (! $activeSmsGateway) {
            return;
        }

        if ($purpose !== 'test_sms') {
            $body = SmsTemplate::smsTempleteToBody($templete->body, $data);
        } else {
            $body = 'It is a Test Sms From '.$activeSmsGateway->gateway_name.' -'.generalSetting()->school_name;
        }

        try {
            if ($activeSmsGateway->gateway_name === 'Twilio') {
                $account_id = $activeSmsGateway->twilio_account_sid;
                $auth_token = $activeSmsGateway->twilio_authentication_token;
                $from_phone_number = $activeSmsGateway->twilio_registered_no;
                if (! $account_id || $auth_token) {
                    return;
                }

                $client = new Client($account_id, $auth_token);
                $result = $client->messages->create($reciver_number, ['from' => $from_phone_number, 'body' => $body]);
                $message = $result;
            } elseif ($activeSmsGateway->gateway_name === 'Msg91') {
                $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
                $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
                $msg91_route = $activeSmsGateway->msg91_route;
                $msg91_country_code = $activeSmsGateway->msg91_country_code;

                if ($reciver_number ) {
                    $curl = curl_init();
                    $url = 'https://api.msg91.com/api/sendhttp.php?mobiles='.
                        $reciver_number.'&authkey='.
                        $msg91_authentication_key_sid.'&route='.
                        $msg91_route.'&sender='.
                        $msg91_sender_id.'&message='.
                        urlencode($body).'&country='.$msg91_country_code;

                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
                    ]);

                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                }
            } elseif ($activeSmsGateway->gateway_name === 'TextLocal') {
                // Config variables. Consult http://api.txtlocal.com/docs for more info.
                $url = $activeSmsGateway->type === 'in' ? 'https://api.textlocal.in/send/?' : 'https://api.txtlocal.com/send/?';
                $test = '0';
                $sender = $activeSmsGateway->textlocal_sender; // This is who the message appears to be from.
                $message = urlencode($body);
                $data = 'username='.$activeSmsGateway->textlocal_username.
                    '&hash='.$activeSmsGateway->textlocal_hash.
                    '&message='.$message.
                    '&sender='.$sender.
                    '&numbers='.$reciver_number.
                    '&test='.$test;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch); // This is the result from the API
                curl_close($ch);
            } elseif ($activeSmsGateway->gateway_name === 'AfricaTalking') {
                $username = $activeSmsGateway->africatalking_username;
                $apiKey = $activeSmsGateway->africatalking_api_key;
                $AT = new AfricasTalking($username, $apiKey);

                $sms_Send = $AT->sms();
                $sms_Send->send(['to' => $reciver_number, 'message' => $body]);
            } elseif ($activeSmsGateway->gateway_name === 'Himalayasms') {
                if ($reciver_number ) {
                    $client = new Http();
                    $request = $client->get('https://sms.techhimalaya.com/base/smsapi/index.php', [
                        'query' => [
                            'key' => $activeSmsGateway->himalayasms_key,
                            'senderid' => $activeSmsGateway->himalayasms_senderId,
                            'campaign' => $activeSmsGateway->himalayasms_campaign,
                            'routeid' => $activeSmsGateway->himalayasms_routeId,
                            'contacts' => $reciver_number,
                            'msg' => $body,
                            'type' => 'text',
                        ],
                        'http_errors' => false,
                    ]);
                    $request->getBody();
                }
            } elseif ($activeSmsGateway->gateway_type === 'custom') {
                send_custom_sms($reciver_number, $body, $activeSmsGateway);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }
    }
}

if (! function_exists('short_code_messege')) {
    function short_code_messege($templete, array $data)
    {
        $templete = str_replace('[class]', @$data['class'], $templete);
        $templete = str_replace('[section]', @$data['section'], $templete);
        $templete = str_replace('[teacher_name]', @$data['teacher_name'], $templete);
        $templete = str_replace('[admin_name]', @$user->full_name, $templete);

        return str_replace('[student_name]', gv($data, 'student_name', @$user->full_name), $templete);
    }
}
