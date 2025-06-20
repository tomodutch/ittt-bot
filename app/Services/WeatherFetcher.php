<?php

namespace App\Services;
use App\Http\Integrations\WeatherAPI\Requests\GetWeatherInformationRequest;
use App\Http\Integrations\WeatherAPI\WeatherAPIConnector;

class WeatherFetcher
{
    public function __construct(
        protected WeatherAPIConnector $connector,
        protected string $apiKey
    ) {
    }

    public function fetchWeather(string $location): mixed
    {
        $request = new GetWeatherInformationRequest(apiKey: $this->apiKey, location: $location);
        $response = $this->connector->send($request);
        return $response->dtoOrFail();
    }
}