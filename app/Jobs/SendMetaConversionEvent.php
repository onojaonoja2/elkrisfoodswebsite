<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMetaConversionEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $customData
     * @param  array{email?: ?string, phone?: ?string, ip?: ?string, user_agent?: ?string}  $userData
     */
    public function __construct(
        public string $eventName,
        public array $customData = [],
        public array $userData = [],
        public ?string $eventSourceUrl = null,
    ) {}

    public function handle(): void
    {
        $pixelId = Setting::get('analytics.meta_pixel_id');
        $token = Setting::get('analytics.meta_capi_token');

        if (! $pixelId || ! $token) {
            return;
        }

        $email = $this->userData['email'] ?? null;
        $phone = $this->userData['phone'] ?? null;

        $userData = array_filter([
            'em' => $email ? [hash('sha256', strtolower(trim($email)))] : null,
            'ph' => $phone ? [hash('sha256', preg_replace('/\D+/', '', $phone) ?: strtolower(trim($phone)))] : null,
            'client_ip_address' => $this->userData['ip'] ?? null,
            'client_user_agent' => $this->userData['user_agent'] ?? null,
        ], fn ($value) => $value !== null && $value !== []);

        $payload = [
            'data' => [array_filter([
                'event_name' => $this->eventName,
                'event_time' => time(),
                'action_source' => 'website',
                'event_source_url' => $this->eventSourceUrl,
                'user_data' => $userData,
                'custom_data' => $this->customData !== [] ? $this->customData : null,
            ], fn ($value) => $value !== null)],
            'access_token' => $token,
        ];

        $testEventCode = Setting::get('analytics.meta_capi_test_event_code');

        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }

        try {
            $response = Http::timeout(5)->post(
                "https://graph.facebook.com/v21.0/{$pixelId}/events",
                $payload,
            );

            if ($response->failed()) {
                Log::warning('Meta Conversions API request failed.', [
                    'status' => $response->status(),
                    'event' => $this->eventName,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Meta Conversions API request errored.', [
                'event' => $this->eventName,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
