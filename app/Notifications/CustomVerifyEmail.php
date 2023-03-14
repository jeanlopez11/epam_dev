<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Lang;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class CustomVerifyEmail extends Notification
{
    use Queueable;
    // public $id;
    // public $hash;
    
    public function __construct()
    {
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

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get('No reply: ').' '.Lang::get('Verify Email Address'))
            ->greeting(Lang::get("Hello ") . $notifiable->name)
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $this->verificationUrl($notifiable),)
            ->line(Lang::get('This link to verify the mail will expire in :count minutes.', ['count' =>  config('auth.passwords.users.expire')]))
            ->line(Lang::get('If you did not create an account, no further action is required.'))
            ->salutation(new HtmlString(
                Lang::get("Regards.").'<br>' .'<strong>'. Lang::get("Team EPAM") . '</strong>'
            ));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    
    //CONFIGURACION TIEMPO VALIDO DEL CORREO DE CONFIRMACION
    protected function verificationUrl($notifiable)
    {
       return URL::temporarySignedRoute(
          'verification.verify',
          Carbon::now()->addMinutes(
            //SOBRE 60 ES LA CONVERSION DEL TIEMPO SOBRE 60SEGUNDOS
             Config::get('auth.verification.expire', config('auth.passwords.users.expire'))),
            //  ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]
               [
                 'id' => $notifiable->getKey(),
                 'hash' => sha1($notifiable->getEmailForVerification()),
               ]     
          ); 
    }
}
