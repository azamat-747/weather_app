<?php

namespace App\Channels;

use App\Interfaces\NotifiableChannel;
use App\Notifications\WeatherNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use function Laravel\Prompts\error;

class MailChannel implements NotifiableChannel
{
    public function send($to, $weatherData): void {
        if (!$to) {
            error('Email address not found');
            exit();
        }
        $notifiable = new AnonymousNotifiable();
        $notifiable->route('mail', $to);
        $notifiable->notify(new WeatherNotification($weatherData));
    }

}
