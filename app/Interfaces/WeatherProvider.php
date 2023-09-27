<?php

namespace App\Interfaces;

interface WeatherProvider
{
    public function getWeather(string $city): array;
}
