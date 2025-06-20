<?php

namespace App\Http\Integrations\WeatherAPI\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use App\Http\Integrations\WeatherAPI\DataTransferObjects\WeatherData;

class GetWeatherInformationRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;
    public function __construct(private string $apiKey, private string $location) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v1/current.json';
    }

    public function defaultQuery(): array
    {
        return [
            'key' => $this->apiKey,
            'q' => $this->location
        ];
    }

    public function createDtoFromResponse(Response $response): mixed {
        $body = $response->json();
        return WeatherData::fromArray($body);
    }
}
