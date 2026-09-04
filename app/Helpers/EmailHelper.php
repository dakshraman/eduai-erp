<?php

/**
 * EmailHelper.php — Email sending helpers.
 *
 * Extracted from Helper.php (P4 DRY cleanup).
 * Must be added to composer.json autoload.files to load globally.
 *
 * Contains: send_mail (template-based), send_mail_without_template,
 * emailTemplate (template lookup).
 */

use App\Jobs\EmailJob;
use App\Models\SmEmailSetting;
use App\Models\SmsTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

if (! function_exists('emailTemplate')) {
    function emailTemplate()
    {
        if (session()->has('email_template')) {
            return session()->get('email_template');
        }

        $email_template = SmsTemplate::where('school_id', Auth::user()->school_id)->first();
        session()->put('email_template', $email_template);

        return session()->get('email_template');

    }
}

if (! function_exists('send_mail')) {
    function send_mail($reciver_email, $receiver_name, $purpose, $data = []): void
    {
        if (! $reciver_email) {
            return;
        }

        $templete = getTempleteDetails($purpose, 'email');
        if (! $templete) {
            return;
        }

        $school_id = Auth::check() && saasSettings('email_settings') ? Auth::user()->school_id : 1;

        $setting = SmEmailSetting::where('school_id', $school_id)->where('active_status', 1)->first();

        if (! $setting) {
            return;
        }

        $sender_email = $setting->from_email;
        $sender_name = $setting->from_name;
        $email_driver = $setting->mail_driver;

        $subject = getTempleteDetails($purpose, 'email')->subject;

        $body = SmsTemplate::emailTempleteToBody(getTempleteDetails($purpose, 'email')->body, $data);
        view('backEnd.email.emailBody', ['body' => $body]);

        try {
            $smtpConfig = null;
            if (Schema::hasTable('sm_email_settings')) {
                if ($email_driver === 'smtp') {
                    $config = Auth::check() ? DB::table('sm_email_settings')
                        ->where('school_id', Auth::user()->school_id)
                        ->where('mail_driver', 'smtp')
                        ->first() :
                        DB::table('sm_email_settings')
                            ->where('mail_driver', 'smtp')
                            ->first();

                    if ($config) {
                        $smtpConfig = [
                            'host' => $config->mail_host,
                            'port' => $config->mail_port,
                            'encryption' => $config->mail_encryption,
                            'username' => $config->mail_username,
                            'password' => $config->mail_password,
                            'from_address' => $config->from_email ?? $config->mail_username,
                            'from_name' => $config->from_name,
                        ];
                    }
                }
            }

            $emailData['driver'] = $email_driver;
            $emailData['reciver_email'] = $reciver_email;
            $emailData['receiver_name'] = $receiver_name;
            $emailData['sender_name'] = $sender_name;
            $emailData['sender_email'] = $sender_email;
            $emailData['subject'] = $subject;
            $emailData['smtp_config'] = $smtpConfig;

            dispatch(new EmailJob($body, $emailData));
        } catch (Exception $exception) {
            Log::info($exception);
        }
    }
}

if (! function_exists('send_mail_without_template')) {
    function send_mail_without_template($reciver_email, $receiver_name, $subject, $view, $compact = []): void
    {

        $school_id = Auth::check() && saasSettings('email_settings') ? Auth::user()->school_id : 1;

        $setting = SmEmailSetting::where('school_id', $school_id)->where('active_status', 1)->first();

        if (! $setting) {
            return;
        }

        $sender_email = $setting->from_email;
        $sender_name = $setting->from_name;
        $email_driver = $setting->mail_driver;
        $view = view($view, $compact);
        try {
            if ($email_driver === 'smtp') {
                if (Schema::hasTable('sm_email_settings')) {
                    $config = Auth::check() ? DB::table('sm_email_settings')
                        ->where('school_id', Auth::user()->school_id)
                        ->where('mail_driver', 'smtp')
                        ->first() :
                        DB::table('sm_email_settings')
                            ->where('mail_driver', 'smtp')
                            ->first();

                    if ($config) {
                        Config::set('mail.mailers.smtp.host', $config->mail_host);
                        Config::set('mail.mailers.smtp.port', $config->mail_port);
                        Config::set('mail.mailers.smtp.encryption', $config->mail_encryption);

                        if ($config->mail_username && $config->mail_password) {
                            Config::set('mail.mailers.smtp.username', $config->mail_username);
                            Config::set('mail.mailers.smtp.password', $config->mail_password);
                        }
                    }
                }
                Mail::send('backEnd.email.emailBody', ['body' => $body], function ($message) use ($reciver_email, $receiver_name, $sender_name, $sender_email, $subject): void {
                    $message->to($reciver_email, $receiver_name)->subject($subject);
                    $message->from($sender_email, $sender_name);
                });
            } elseif ($email_driver === 'php') {
                $message = (string) $view;
                $headers = "From: <{$sender_email}> \r\n";
                $headers .= "Reply-To: {$receiver_name} <{$reciver_email}> \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8\r\n";
                @mail($reciver_email, $subject, $message, $headers);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }
    }
}
