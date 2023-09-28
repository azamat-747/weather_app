<?php

namespace App\Providers\Weather;

use App\Interfaces\WeatherProvider;
use App\Weather\Traits\FetchDataTraits;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\error;
use function Laravel\Prompts\spin;

class AccuWeatherProvider implements WeatherProvider
{
    use FetchDataTraits;

    public function getWeather(string $city): array
    {
        $api = config('services.accu_weather.api');
        $geocoding = spin(
            fn () => Http::get(Http::get("http://dataservice.accuweather.com/locations/v1/cities/search?apikey={$api}&q={$city}")),
            'Fetching response...'
        );
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
            error('City not found or The allowed number of requests has been exceeded.');
            exit();
        }
        $url = "http://dataservice.accuweather.com/currentconditions/v1/{$key}?apikey={$api}&language=ru-RU&details=true";
        $data = $this->fetchData($url);
        return [
            'city' => $city,
            'text' => $data[0]['WeatherText'],
            'temp' => $data[0]['Temperature']['Metric']['Value'] . '°',
        ];
    }
}
