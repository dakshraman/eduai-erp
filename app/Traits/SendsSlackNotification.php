<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendsSlackNotification
{
    /**
     * Send notification to Slack
     *
     * @param  string  $type  (info, success, warning, error)
     */
    protected function sendSlackNotification(string $message, string $type = 'info'): void
    {
        $webhookUrl = config('services.slack.webhook_url');

        if (empty($webhookUrl)) {
            return;
        }

        try {
            $color = $this->getSlackColor($type);
            $icon = $this->getSlackIcon($type);

            $payload = [
                'attachments' => [
                    [
                        'color' => $color,
                        'title' => $icon.' '.$this->getSlackTitle($type),
                        'text' => $message,
                        'footer' => config('app.name').' | '.config('app.url'),
                        'ts' => now()->timestamp,
                        'fields' => [
                            [
                                'title' => 'Environment',
                                'value' => config('app.env'),
                                'short' => true,
                            ],
                            [
                                'title' => 'Server',
                                'value' => gethostname(),
                                'short' => true,
                            ],
                        ],
                    ],
                ],
            ];

            Http::timeout(5)->post($webhookUrl, $payload);
        } catch (Exception $e) {
            Log::warning('Failed to send Slack notification: '.$e->getMessage());
        }
    }

    /**
     * Send notification for reset start
     */
    protected function notifyResetStarted(): void
    {
        $this->sendSlackNotification(
            '🚀 Database reset process has been initiated.',
            'info'
        );
    }

    /**
     * Send notification for reset completion
     */
    protected function notifyResetCompleted(string $startTime, string $endTime): void
    {
        $duration = now()->parse($startTime)->diffForHumans(now()->parse($endTime), true);

        $this->sendSlackNotification(
            "✅ Database reset completed successfully!\n\n".
            "Started: {$startTime}\n".
            "Completed: {$endTime}\n".
            "Duration: {$duration}",
            'success'
        );
    }

    /**
     * Send notification for reset failure
     */
    protected function notifyResetFailed(string $errorMessage, string $startTime): void
    {
        $this->sendSlackNotification(
            "❌ Database reset failed!\n\n".
            "Started: {$startTime}\n".
            'Failed at: '.now()->toDateTimeString()."\n\n".
            "Error: {$errorMessage}",
            'error'
        );
    }

    /**
     * Get Slack color based on type
     */
    protected function getSlackColor(string $type): string
    {
        return match ($type) {
            'success' => '#36a64f',
            'error' => '#ff0000',
            'warning' => '#ff9900',
            default => '#3AA3E3',
        };
    }

    /**
     * Get Slack icon based on type
     */
    protected function getSlackIcon(string $type): string
    {
        return match ($type) {
            'success' => '✅',
            'error' => '❌',
            'warning' => '⚠️',
            default => 'ℹ️',
        };
    }

    /**
     * Get Slack title based on type
     */
    protected function getSlackTitle(string $type): string
    {
        return match ($type) {
            'success' => 'Success',
            'error' => 'Error',
            'warning' => 'Warning',
            default => 'Information',
        };
    }
}
