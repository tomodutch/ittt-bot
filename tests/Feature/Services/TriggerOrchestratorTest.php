<?php

namespace Tests\Feature\Services;

use App\Models\Schedule;
use App\Models\Trigger;
use App\Models\TriggerExecution;
use App\Services\TriggerOrchestrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TriggerOrchestratorTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_empty()
    {
        $orchestrator = new TriggerOrchestrator;
        $orchestrator->process(collect());
        $this->assertEquals(0, TriggerExecution::count());
    }

    public function test_schedule()
    {
        $amount = 10;
        $triggers = Schedule::factory()->createMany($amount);
        $orchestrator = new TriggerOrchestrator;
        $orchestrator->process($triggers);
        $this->assertEquals($amount, TriggerExecution::count(), 'Expected triggers to be scheduled for execution');
    }

    public function test_deduplication()
    {
        $amount = 10;
        $trigger = Trigger::factory()->create();
        $schedules = Schedule::factory()->createMany($amount);
        foreach ($schedules as $schedule) {
            $schedule->trigger()->associate($trigger);
        }

        $orchestrator = new TriggerOrchestrator;
        $orchestrator->process($schedules);
        $this->assertEquals(1, TriggerExecution::count(), 'Expected 1 triggerExecution to be created');
        foreach ($schedules as $schedule) {
            $this->assertNotEmpty($schedule->triggerExecutions());
        }
    }
}
