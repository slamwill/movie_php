<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Crypt;

class AccountResetPassword extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
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
		$arr = ['email' => $notifiable->email, 'name' => $notifiable->name];
		
        return (new MailMessage)
            ->line('您收到此電子郵件是因為我們收到了您帳戶的密碼重置請求。')
            //->action('重置密碼', url(config('app.url').route('password.reset', $this->token, false)))
			//->action('重置密碼', url(config('app.url').route('password.reset', [$this->token, 'email=' . $notifiable->email], false)))
			//->action('重置密碼', url(config('app.url').route('password.reset', [$this->token, 'name=' . $notifiable->name], false)))
			->action('重置密碼', url(config('app.url').route('password.reset', [$this->token, 'q=' . Crypt::encryptString( json_encode($arr) ) ], false)))
            ->line('如果您未請求重置密碼，則無需採取進一步措施。');
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
}
