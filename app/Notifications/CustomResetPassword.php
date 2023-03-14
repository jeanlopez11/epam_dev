<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use PhpParser\Node\Expr\AssignOp\Concat;

class CustomResetPassword extends Notification
{
    use Queueable;
    public $token;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject(Lang::get('No reply: ').' '.(Lang::get('Reset Password Notification')))
        ->greeting(Lang::get("Hello ") . $notifiable->name)
        ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
        ->action(Lang::get('Reset Password'), $this->verificationUrl($notifiable))
        ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
        ->line(Lang::get('If you did not request a password reset, no further action is required.'))
        ->salutation(new HtmlString(
            Lang::get("Regards.").'<br>' .'<strong>'. Lang::get("Team EPAM") . '</strong>'
        ));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
    #LIMITANDO TAMBIEN EL TIEMPO DE VALIDEZ DEL LINK DE RESET PASSWORD
    protected function verificationUrl($notifiable)
    {
       return URL::temporarySignedRoute(
          'password.reset',
          Carbon::now()->addMinutes(
            //SOBRE 60 ES LA CONVERSION DEL TIEMPO SOBRE 60SEGUNDOS
             Config::get('auth.verification.expire', config('auth.passwords.users.expire'))),
            //  ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]
               [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),	
               ]     
          ); 
    }
}
