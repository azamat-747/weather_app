<?php

namespace App\Channels;

use App\Interfaces\NotifiableChannel;
use App\Notifications\WeatherNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use function Laravel\Prompts\error;

class TelegramChannel implements NotifiableChannel
{
    public function send($to, $weatherData): void {
        if (!$to) {
            error('Chat ID not found');
            exit();
        }
        $notifiable = new AnonymousNotifiable();
        $notifiable->route('telegram', $to);
        $notifiable->notify(new WeatherNotification($weatherData));
    }
}
