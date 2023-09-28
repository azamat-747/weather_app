<?php

namespace App\Weather\Traits;

use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\error;

trait FetchDataTraits
{
    protected function fetchData($url) {
        $response = Http::get($url);
        if ($response->status() !== 200) {
            error('Failed to get weather data');
        }
        return json_decode($response->body(), true);
    }
}
