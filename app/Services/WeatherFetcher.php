<?php

namespace App\Services;

use App\Contracts\WeatherFetcherContract;
use App\Http\Integrations\WeatherAPI\DataTransferObjects\WeatherData;
use App\Http\Integrations\WeatherAPI\Requests\GetWeatherInformationRequest;
use App\Http\Integrations\WeatherAPI\WeatherAPIConnector;

class WeatherFetcher implements WeatherFetcherContract
{
    public function __construct(
        protected WeatherAPIConnector $connector,
        protected string $apiKey
    ) {}

    public function fetchWeather(string $location): WeatherData
    {
        $request = new GetWeatherInformationRequest(apiKey: $this->apiKey, location: $location);
        $response = $this->connector->send($request);

        return $response->dtoOrFail();
    }
}
