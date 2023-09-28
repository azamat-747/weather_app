<?php

namespace App\Weather;

use Illuminate\Support\Facades\Config;
use function Laravel\Prompts\info;

class Weather
{
    public function provider($provider, $city) {
        $providerClass = Config::get("weather.providers.$provider");
        if (!$providerClass || !class_exists($providerClass)) {
            info("Unsupported provider: $provider");
            exit();
        }
        $weatherProvider = app($providerClass);
        return $weatherProvider->getWeather($city);
    }
}
