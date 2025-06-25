<?php

namespace Tests\Feature\Services;

use App\Contracts\WeatherFetcherContract;
use App\Domain\Workflow\StepHandlerResolver;
use App\Domain\Workflow\StepProcessor;
use App\Enums\ExecutionStatus;
use App\Models\Trigger;
use App\Models\TriggerExecution;
use App\Domain\Workflow\TriggerExecutionProcessor;
use Database\Factories\WorkflowFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\InMemoryWeatherFetcher;
use Tests\Helpers\WeatherFactory;
use Tests\TestCase;

class TriggerExecutionProcessorTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testEmpty()
    {
        $stepHandlerResolver = new StepHandlerResolver();
        $stepProcessor = new StepProcessor($stepHandlerResolver);
        $execution = TriggerExecution::factory()->idle()->create();
        $trigger = $execution->trigger;
        $trigger->steps()->delete();
        $processor = new TriggerExecutionProcessor($stepProcessor);
        $processor->process($execution);
        $this->assertEquals(ExecutionStatus::Finished, $execution->status_code);
    }

    public function testFetchWeather()
    {
        $this->app->bind(WeatherFetcherContract::class, function () {
            return new InMemoryWeatherFetcher([
                "London" => WeatherFactory::getLondonWeather()
            ]);
        });
        
        $stepHandlerResolver = new StepHandlerResolver();
        $stepProcessor = new StepProcessor($stepHandlerResolver);
        $execution = TriggerExecution::factory()->has(Trigger::factory())->create();
        $execution->trigger->steps()->saveMany(WorkflowFactory::createWeatherWorkflow());

        $processor = new TriggerExecutionProcessor($stepProcessor);
        $processor->process($execution);

        $this->assertEquals(ExecutionStatus::Finished, $execution->status_code);
    }
}