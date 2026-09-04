<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

/**
 * EmailJob — Sends emails via php mail() or SMTP.
 * Uses the `backEnd.email.emailBody` view template.
 * Dispatched from: Helper.php (sendMail) and Traits/NotificationSend.php
 *
 * Note: SendEmailJob is a separate job using backEnd.emails.mail template.
 * Both are needed — do not merge without auditing all dispatch call-sites.
 */
class EmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $body;

    protected $details = [];

    public function __construct($body, $data)
    {
        $this->body = $body;
        $this->details = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->details['driver'] == 'php') {
            $receiver_name = $this->details['receiver_name'];
            $reciver_email = $this->details['reciver_email'];
            $sender_email = $this->details['sender_email'];
            $subject = $this->details['subject'];

            $view = view('backEnd.email.emailBody', ['body' => $this->body]);
            $message = (string) $view;
            $headers = "From: <{$sender_email}> \r\n";
            $headers .= "Reply-To: {$receiver_name} <{$reciver_email}> \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=utf-8\r\n";
            @mail($reciver_email, $subject, $message, $headers);
        } elseif ($this->details['driver'] == 'smtp') {

            $smtpConfig = $this->details['smtp_config'] ?? null;
            if ($smtpConfig) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.from.address', $smtpConfig['from_address']);
                Config::set('mail.from.name', $smtpConfig['from_name']);
                Config::set('mail.mailers.smtp.host', $smtpConfig['host']);
                Config::set('mail.mailers.smtp.port', $smtpConfig['port']);
                Config::set('mail.mailers.smtp.encryption', $smtpConfig['encryption']);

                if (! empty($smtpConfig['username']) && ! empty($smtpConfig['password'])) {
                    Config::set('mail.mailers.smtp.username', $smtpConfig['username']);
                    Config::set('mail.mailers.smtp.password', $smtpConfig['password']);
                }
            }

            Mail::send('backEnd.email.emailBody', ['body' => $this->body], function ($message): void {
                $message->to($this->details['reciver_email'], $this->details['receiver_name'])->subject($this->details['subject']);
                $message->from($this->details['sender_email'], $this->details['sender_name']);
            });
        }
    }
}
