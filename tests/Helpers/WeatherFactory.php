<?php

namespace Tests\Helpers;

use App\Http\Integrations\WeatherAPI\DataTransferObjects\WeatherData;

class WeatherFactory
{
    protected static string $defaultFixturePath = __DIR__.'/../Fixtures/Saloon/london_weather.json';

    /**
     * Returns the London weather data as an associative array
     */
    public static function getLondonWeather(): WeatherData
    {
        if (! file_exists(self::$defaultFixturePath)) {
            throw new \RuntimeException('Fixture file not found: '.self::$defaultFixturePath);
        }

        $json = file_get_contents(self::$defaultFixturePath);

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON in fixture file: '.json_last_error_msg());
        }

        return WeatherData::fromArray(json_decode($data['data'], true));
    }
}
