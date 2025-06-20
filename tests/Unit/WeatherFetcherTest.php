<?php

namespace Tests\Unit;

use App\Http\Integrations\WeatherAPI\Requests\GetWeatherInformationRequest;
use App\Services\WeatherFetcher;
use App\Http\Integrations\WeatherAPI\WeatherAPIConnector;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Spatie\Snapshots\MatchesSnapshots;

class WeatherFetcherTest extends TestCase
{
    use MatchesSnapshots;

    public function testGetWeatherInformation()
    {
        $mockClient = new MockClient([
            GetWeatherInformationRequest::class => MockResponse::fixture("london_weather"),
        ]);
        $connector = new WeatherAPIConnector("http://example.com");
        $connector->withMockClient($mockClient);
        $fetcher = new WeatherFetcher($connector, "key");
        $result = $fetcher->fetchWeather('London');

        $mockClient->assertSent(GetWeatherInformationRequest::class);
        $this->assertMatchesJsonSnapshot(json_encode($result, JSON_PRETTY_PRINT));
    }
}