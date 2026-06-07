<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        $frontendUrl = config('app.frontend_url', config('app.url'));

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        // Forward signature params to frontend so a SPA can call the API directly
        $params = parse_url($signedUrl, PHP_URL_QUERY);

        return "{$frontendUrl}/verify-email/{$notifiable->getKey()}?{$params}";
    }
}
