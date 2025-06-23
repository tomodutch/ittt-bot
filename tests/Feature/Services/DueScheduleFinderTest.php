<?php
use App\Services\DueScheduleFinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Schedule;
use App\Models\TriggerExecution;
use Carbon\Carbon;

class DueScheduleFinderTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_empty()
    {
        $service = new DueScheduleFinder();
        $this->assertEmpty($service->find(), "should have no results");
    }

    public function test_with_one_result()
    {
        Carbon::setTestNow(now());
        Schedule::factory()->once()->create([
            "one_time_at" => Carbon::getTestNow()->subSeconds(20)
        ]);

        $service = new DueScheduleFinder();
        $result = $service->find();
        $this->assertCount(1, $result, "Expected to find only 1 due schedule");
    }

    public function test_with_already_executed()
    {
        Carbon::setTestNow(now());
        $schedule = Schedule::factory()->once()->create([
            "one_time_at" => Carbon::getTestNow()->subSeconds(20)
        ]);

        $schedule->triggerExecutions()->save(TriggerExecution::factory()->create([
            "schedule_id" => $schedule->id,
        ]));

        $service = new DueScheduleFinder();
        $result = $service->find();
        $this->assertEmpty($result, "Expected to find 0 schedules");
    }

    public function test_with_daily()
    {
        Carbon::setTestNowAndTimezone(now("UTC"), "UTC");
        Schedule::factory()->daily()->create([
            "days_of_the_week" => [],
            'time' => Carbon::getTestNow()->format('H:i:s'),
        ]);

        $service = new DueScheduleFinder();
        $result = $service->find();
        $this->assertCount(1, $result, "Expected to find only 1 due schedule");
    }

    public function test_with_weekly_wrong_day()
    {
        // Monday
        Carbon::setTestNowAndTimezone(Carbon::create(2025, 6, 23, 13), "UTC");
        Schedule::factory()->weekly()->create([
            "days_of_the_week" => [Carbon::WEDNESDAY],
            'time' => Carbon::getTestNow()->format('H:i:s'),
        ]);

        $service = new DueScheduleFinder();
        $result = $service->find();
        $this->assertEmpty($result, "Expected to find no due schedule");
    }

    public function test_with_weekly_correct_day()
    {
        // Monday
        Carbon::setTestNowAndTimezone(Carbon::create(2025, 6, 23, 13), "UTC");
        Schedule::factory()->weekly()->create([
            "days_of_the_week" => [Carbon::MONDAY],
            'time' => Carbon::getTestNow()->format('H:i:s'),
        ]);

        $service = new DueScheduleFinder();
        $result = $service->find();
        $this->assertCount(1, $result, "Expected to find 1 due schedule");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNowAndTimezone();
    }
}