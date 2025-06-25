<?php

namespace Tests\Helpers;

use App\Contracts\WeatherFetcherContract;
use App\Http\Integrations\WeatherAPI\DataTransferObjects\WeatherData;

class InMemoryWeatherFetcher implements WeatherFetcherContract
{
    public function __construct(private array $locationWeatherData = [])
    {

    }
    public function fetchWeather(string $location): WeatherData
    {
        return $this->locationWeatherData[$location];
    }
}