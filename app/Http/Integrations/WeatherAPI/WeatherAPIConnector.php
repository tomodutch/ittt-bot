<?php

namespace App\Http\Integrations\WeatherAPI;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;

class WeatherAPIConnector extends Connector
{
    use AcceptsJson;
    use HasTimeout;

    public function __construct(private string $baseUri) {

    }

    /**
     * Connect timeout of 5 seconds
     * @var int
     */
    protected int $connectTimeout = 5;

    /**
     * Request timeout of 15 seconds
     * @var int
     */
    protected int $requestTimeout = 15;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return $this->baseUri;
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
