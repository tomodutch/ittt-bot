<?php

namespace App\Providers;

use App\Contracts\WeatherFetcherContract;
use App\Domain\Workflow\Contracts\StepHandlerResolverContract;
use App\Services\WeatherFetcher;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory)->create(
                new Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key')
                )
            );
        });
        $this->app->bind(
            \App\Domain\Workflow\Contracts\StepProcessorContract::class,
            \App\Domain\Workflow\StepProcessor::class
        );

        $this->app->bind(
            StepHandlerResolverContract::class,
            \App\Domain\Workflow\StepHandlerResolver::class
        );

        $this->app->bind(
            WeatherFetcherContract::class,
            function () {
                return new WeatherFetcher(
                    app(\App\Http\Integrations\WeatherAPI\WeatherAPIConnector::class),
                    env('WEATHERAPI_KEY', 'default_api_key')
                );
            }
        );

        $this->app->bind(
            \App\Http\Integrations\WeatherAPI\WeatherAPIConnector::class,
            function () {
                return new \App\Http\Integrations\WeatherAPI\WeatherAPIConnector(
                    baseUri: env('WEATHERAPI_BASE_URI', 'https://api.weatherapi.com')
                );
            }
        );
    }
}
