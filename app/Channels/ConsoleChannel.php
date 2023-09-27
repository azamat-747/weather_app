<?php

namespace App\Channels;

use App\Interfaces\NotifiableChannel;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

class ConsoleChannel implements NotifiableChannel
{
    public function send($to, $weatherData): void {
        info('Текущая погода:');
        note('Город: ' . $weatherData['city']);
        note('Статус: ' . $weatherData['text']);
        note('Температура: ' . $weatherData['temp']);
    }
}
