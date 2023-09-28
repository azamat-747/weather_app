<?php

namespace App\Console\Commands;

use App\Weather\Facades\WeatherFacade;
use App\Weather\Traits\UiTraits;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Config;
use function Laravel\Prompts\info;

class Weather extends Command implements PromptsForMissingInput
{
    use UiTraits;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather {provider} {city} {channel} {to?}';

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

        $weatherData = WeatherFacade::provider($channel, $city);

        $channelClass = Config::get("services.channels.$channel");
        if (!$channelClass || !class_exists($channelClass)) {
            info("Unsupported channel: $channel");
            exit();
        }
        $notifiableChannel = app($channelClass);
        $notifiableChannel->send($to, $weatherData);
        $this->components->info('Success');
    }
}
