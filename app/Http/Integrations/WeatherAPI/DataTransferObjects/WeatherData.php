<?php

namespace App\Http\Integrations\WeatherAPI\DataTransferObjects;

class WeatherCondition
{
    public function __construct(
        public readonly string $text,
        public readonly string $iconUrl,
        public readonly int $conditionCode,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            text: $data['text'],
            iconUrl: $data['icon'],
            conditionCode: $data['code'],
        );
    }
}

class WeatherLocation
{
    public function __construct(
        public readonly string $locationName,
        public readonly string $locationRegion,
        public readonly string $locationCountry,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $timeZoneId,
        public readonly int $localTimeEpoch,
        public readonly string $localTime,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            locationName: $data['name'],
            locationRegion: $data['region'],
            locationCountry: $data['country'],
            latitude: $data['lat'],
            longitude: $data['lon'],
            timeZoneId: $data['tz_id'],
            localTimeEpoch: $data['localtime_epoch'],
            localTime: $data['localtime'],
        );
    }
}

class WeatherCurrent
{
    public function __construct(
        public readonly int $lastUpdatedEpoch,
        public readonly string $lastUpdated,
        public readonly float $temperatureCelsius,
        public readonly float $temperatureFahrenheit,
        public readonly int $isDay,
        public readonly WeatherCondition $weatherCondition,
        public readonly float $windMph,
        public readonly float $windKph,
        public readonly int $windDegree,
        public readonly string $windDirection,
        public readonly float $pressureMillibar,
        public readonly float $pressureInches,
        public readonly float $precipitationMillimeters,
        public readonly float $precipitationInches,
        public readonly int $humidityPercent,
        public readonly int $cloudCoveragePercent,
        public readonly float $feelsLikeCelsius,
        public readonly float $feelsLikeFahrenheit,
        public readonly float $windChillCelsius,
        public readonly float $windChillFahrenheit,
        public readonly float $heatIndexCelsius,
        public readonly float $heatIndexFahrenheit,
        public readonly float $dewPointCelsius,
        public readonly float $dewPointFahrenheit,
        public readonly float $visibilityKilometers,
        public readonly float $visibilityMiles,
        public readonly float $uvIndex,
        public readonly float $gustMph,
        public readonly float $gustKph,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            lastUpdatedEpoch: $data['last_updated_epoch'],
            lastUpdated: $data['last_updated'],
            temperatureCelsius: $data['temp_c'],
            temperatureFahrenheit: $data['temp_f'],
            isDay: $data['is_day'],
            weatherCondition: WeatherCondition::fromArray($data['condition']),
            windMph: $data['wind_mph'],
            windKph: $data['wind_kph'],
            windDegree: $data['wind_degree'],
            windDirection: $data['wind_dir'],
            pressureMillibar: $data['pressure_mb'],
            pressureInches: $data['pressure_in'],
            precipitationMillimeters: $data['precip_mm'],
            precipitationInches: $data['precip_in'],
            humidityPercent: $data['humidity'],
            cloudCoveragePercent: $data['cloud'],
            feelsLikeCelsius: $data['feelslike_c'],
            feelsLikeFahrenheit: $data['feelslike_f'],
            windChillCelsius: $data['windchill_c'],
            windChillFahrenheit: $data['windchill_f'],
            heatIndexCelsius: $data['heatindex_c'],
            heatIndexFahrenheit: $data['heatindex_f'],
            dewPointCelsius: $data['dewpoint_c'],
            dewPointFahrenheit: $data['dewpoint_f'],
            visibilityKilometers: $data['vis_km'],
            visibilityMiles: $data['vis_miles'],
            uvIndex: $data['uv'],
            gustMph: $data['gust_mph'],
            gustKph: $data['gust_kph'],
        );
    }
}

class WeatherData
{
    public function __construct(
        public readonly WeatherLocation $location,
        public readonly WeatherCurrent $current,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            location: WeatherLocation::fromArray($data['location']),
            current: WeatherCurrent::fromArray($data['current']),
        );
    }
}
