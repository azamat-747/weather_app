<?php

namespace App\Console\Commands;

use App\Notifications\WeatherNotification;
use App\Services\WeatherService;
use Illuminate\Console\Command;
use Illuminate\Notifications\AnonymousNotifiable;
use InvalidArgumentException;

class Weather extends Command
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather {provider} {city} {channel=console} {to?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get current weather';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $provider = $this->argument('provider');
        $city = $this->argument('city');
        $channel = $this->argument('channel');
        $to = $this->argument('to');

        $weatherData = $this->weatherService->getWeather($provider, $city);

        $notifiable = $this->getNotifiable($channel, $to, $weatherData);
        $notifiable->notify(new WeatherNotification($weatherData));
    }

    protected function getNotifiable($channel, $to, $weatherData)
    {
        $notifiable = new AnonymousNotifiable();
        switch ($channel) {
            case 'Mail':
                return $notifiable->route('mail', $to);
            case 'Telegram':
                return $notifiable->route('telegram', $to);
            case 'Console':
                $this->info('Текущая погода:');
                $this->comment('Город: ' . $weatherData['city']);
                $this->comment('Статус: ' . $weatherData['text']);
                $this->comment('Температура: ' . $weatherData['temp']);
                break;
            default:
                throw new InvalidArgumentException("Unsupported channel: $channel");
        }
        return $notifiable;
    }
}
