<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OneTimePasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Відновлення паролю на сайті GYMZONE')
            ->greeting('Вітаємо!')
            ->line('Ваш одноразовий пароль для входу:')
            ->line('<strong style="font-size:22px">' . $this->password . '</strong>')
            ->line('Після входу обовʼязково змініть пароль у своєму профілі!')
            ->line('Якщо ви не надсилали запит на відновлення паролю, просто проігноруйте цей лист.');
    }
} 