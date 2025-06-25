<?php

namespace App\Contracts;

use App\Http\Integrations\WeatherAPI\DataTransferObjects\WeatherData;

interface WeatherFetcherContract
{
        public function fetchWeather(string $location): WeatherData;
}