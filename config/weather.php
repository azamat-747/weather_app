<?php

use App\Providers\Weather\AccuWeatherProvider;
use App\Providers\Weather\OpenWeatherProvider;

return [
    'providers' => [
        'open-weather' => OpenWeatherProvider::class,
        'accu-weather' => AccuWeatherProvider::class
    ]
];
