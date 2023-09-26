<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class WeatherNotification extends Notification
{
    use Queueable;

    protected $weatherData;

    /**
     * Create a new notification instance.
     */
    public function __construct($weatherData)
    {
        $this->weatherData = $weatherData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'telegram'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Current Weather')
            ->line('Current weather information...')->view('email.weather', ['weather' => $this->weatherData]);
    }

    public function toTelegram($notifiable) {
        $chatId = '-'.$notifiable->routes['telegram'];
        return TelegramMessage::create()
            ->to($chatId)
            ->content("*Текущая погода:*")
            ->line("\n")
            ->line("Город: {$this->weatherData['city']}")
            ->line("Статус: {$this->weatherData['text']}")
            ->line("Температура: {$this->weatherData['temp']}");
    }
}
