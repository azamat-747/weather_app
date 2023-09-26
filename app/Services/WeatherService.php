<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getWeather($provider, $city) {
        switch ($provider) {
            case "open-weather":
                $api = config('services.open_weather.api');
                $geocoding = Http::get("http://api.openweathermap.org/geo/1.0/direct?q={$city}&limit=5&appid={$api}");
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
                    throw new \Exception('City not found or The allowed number of requests has been exceeded.');
                }
                if ($lat && $lon) {
                    $current = "hourly,daily,minutely";
                    $url = "https://api.openweathermap.org/data/3.0/onecall?lat={$lat}&lon={$lon}&exclude={$current}&units=metric&appid={$api}";
                    $data = $this->fetchData($url);
                    $weatherData = [
                        'city' => $city,
                        'text' => $data['current']['weather'][0]['main'],
                        'temp' => $data['current']['temp'] . '°',
                    ];
                } else {
                    throw new \Exception('City not found');
                }
                break;
            case "accu-weather":
                $api = config('services.accu_weather.api');
                $geocoding = Http::get("http://dataservice.accuweather.com/locations/v1/cities/search?apikey={$api}&q={$city}");
                if ($geocoding->status() == 200) {
                    $key = null;
                    $cityArray = json_decode($geocoding->body(), true);
                    foreach ($cityArray as $c) {
                        if ($c['EnglishName'] === $city) {
                            $key = $c['Key'];
                            break;
                        }
                    }
                } else {
                    throw new \Exception('City not found or The allowed number of requests has been exceeded.');
                }
                $url = "http://dataservice.accuweather.com/currentconditions/v1/{$key}?apikey={$api}&language=ru-RU&details=true";
                $data = $this->fetchData($url);
                $weatherData = [
                    'city' => $city,
                    'text' => $data[0]['WeatherText'],
                    'temp' => $data[0]['Temperature']['Metric']['Value'] . '°',
                ];
                break;
            default:
                throw new \Exception('Unsupported weather provider');
        }

        return $weatherData;
    }

    protected function fetchData($url) {
        $response = Http::get($url);
        if ($response->status() !== 200) {
            throw new \Exception('Failed to get weather data');
        }
        return json_decode($response->body(), true);
    }
}
