<?php

namespace App\Interfaces;

interface NotifiableChannel
{
    public function send($to, $weatherData): void;
}
