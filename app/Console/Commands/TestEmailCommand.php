<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('email:test {email} {--school=1 : The school ID for email settings}')]
#[Description('Send a test email to verify configuration')]
class TestEmailCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $schoolId = $this->option('school');

        $this->info("Sending test email to {$email} for school {$schoolId}...");

        try {
            // Using standard Laravel mail setup for quick test. 
            // Depending on how Infix Edu sets config per school dynamically, you might need to load it here.
            Mail::raw('This is a test email sent from the Infix Edu artisan command.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from Infix Edu Artisan Command');
            });

            $this->info("Test email sent successfully to {$email}.");
        } catch (\Exception $e) {
            $this->error("Failed to send test email.");
            $this->error($e->getMessage());
        }
    }
}
