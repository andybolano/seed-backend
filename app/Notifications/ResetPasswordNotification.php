<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $frontendUrl = config('app.frontend_url', config('app.url'));
        $url = "{$frontendUrl}/reset-password?token={$this->token}&email={$notifiable->getEmailForPasswordReset()}";

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Restablecer contraseña')
            ->line('Recibiste este correo porque solicitaste restablecer tu contraseña.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en ' . config('auth.passwords.users.expire') . ' minutos.')
            ->line('Si no solicitaste esto, ignora este correo.');
    }
}
