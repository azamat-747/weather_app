<?php

namespace App\Providers\Weather;

use App\Interfaces\WeatherProvider;
use App\Traits\Weather\FetchDataTraits;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\error;
use function Laravel\Prompts\spin;

class OpenWeatherProvider implements WeatherProvider
{
    use FetchDataTraits;

    public function getWeather(string $city): array
    {
        $api = config('services.open_weather.api');
        $geocoding = spin(
            fn () => Http::get("http://api.openweathermap.org/geo/1.0/direct?q={$city}&limit=5&appid={$api}"),
            'Fetching response...'
        );
        $lat = null;
        $lon = null;
        if ($geocoding->status() == 200) {
            $cityArray = json_decode($geocoding->body(), true);
            foreach ($cityArray as $c) {
                if ($c['name'] === $city && isset($c['lat'], $c['lon'])) {
                    $lat = $c['lat'];
                    $lon = $c['lon'];
                    break;
                }
            }
        } else {
            error('City not found or The allowed number of requests has been exceeded.');
            exit();
        }
        if ($lat && $lon) {
            $current = "hourly,daily,minutely";
            $url = "https://api.openweathermap.org/data/3.0/onecall?lat={$lat}&lon={$lon}&exclude={$current}&units=metric&appid={$api}";
            $data = $this->fetchData($url);
            return [
                'city' => $city,
                'text' => $data['current']['weather'][0]['main'],
                'temp' => $data['current']['temp'] . '°',
            ];
        } else {
            error('City not found');
            exit();
        }
    }
}
