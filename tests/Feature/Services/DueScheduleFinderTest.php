<?php

use App\Models\Schedule;
use App\Models\TriggerExecution;
use App\Services\DueScheduleFinder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DueScheduleFinderTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_empty()
    {
        $result = $this->findDueSchedules();
        $this->assertEmpty($result, 'should have no results');
    }

    public function test_with_one_result()
    {
        Carbon::setTestNow(now());
        Schedule::factory()->once()->create([
            'one_time_at' => Carbon::getTestNow()->subSeconds(20),
        ]);

        $result = $this->findDueSchedules();
        $this->assertCount(1, $result, 'Expected to find only 1 due schedule');
    }

    public function test_with_already_executed()
    {
        Carbon::setTestNow(now());
        $schedule = Schedule::factory()->once()->create([
            'one_time_at' => Carbon::getTestNow()->subSeconds(20),
        ]);

        $schedule->triggerExecutions()->save(TriggerExecution::factory()->create());

        $result = $this->findDueSchedules();
        $this->assertEmpty($result, 'Expected to find 0 schedules');
    }

    public function test_with_daily()
    {
        Carbon::setTestNowAndTimezone(now('UTC'), 'UTC');
        Schedule::factory()->daily()->create([
            'days_of_the_week' => [],
            'time' => Carbon::getTestNow()->format('H:i:s'),
        ]);

        $result = $this->findDueSchedules();
        $this->assertCount(1, $result, 'Expected to find only 1 due schedule');
    }

    public function test_with_weekly_wrong_day()
    {
        // Monday
        Carbon::setTestNowAndTimezone(Carbon::create(2025, 6, 23, 13), 'UTC');
        Schedule::factory()->weekly()->create([
            'days_of_the_week' => [Carbon::WEDNESDAY],
            'time' => Carbon::getTestNow()->format('H:i:s'),
        ]);

        $result = $this->findDueSchedules();
        $this->assertEmpty($result, 'Expected to find no due schedule');
    }

    public function test_with_weekly_correct_day()
    {
        // Monday
        Carbon::setTestNowAndTimezone(Carbon::create(2025, 6, 23, 13), 'UTC');
        Schedule::factory()->weekly()->create([
            'days_of_the_week' => [Carbon::MONDAY],
            'time' => Carbon::getTestNow()->format('H:i:s'),
        ]);

        $result = $this->findDueSchedules();
        $this->assertCount(1, $result, 'Expected to find 1 due schedule');
    }

    public function test_daily_schedule_with_future_time_not_due()
    {
        Carbon::setTestNowAndTimezone(now('UTC'), 'UTC');

        Schedule::factory()->daily()->create([
            'time' => Carbon::getTestNow()->addMinutes(10)->format('H:i:s'),
        ]);

        $result = $this->findDueSchedules();
        $this->assertEmpty($result, 'Expected no daily schedule due because time is in the future');
    }

    public function test_weekly_schedule_correct_day_but_future_time_not_due()
    {
        // Set current time to Monday 13:00 UTC
        Carbon::setTestNowAndTimezone(Carbon::create(2025, 6, 23, 13, 0, 0), 'UTC');

        Schedule::factory()->weekly()->create([
            'days_of_the_week' => [Carbon::MONDAY],
            'time' => Carbon::getTestNow()->addHour()->format('H:i:s'), // 14:00
        ]);

        $result = $this->findDueSchedules();
        $this->assertEmpty($result, 'Expected no weekly schedule due because time is in the future');
    }

    public function test_schedule_due_with_different_timezone()
    {
        // UTC now is 12:00
        Carbon::setTestNowAndTimezone(Carbon::create(2025, 6, 23, 12, 0, 0), 'UTC');

        // Schedule in timezone Asia/Tokyo (UTC+9), time is 21:00 local time
        Schedule::factory()->daily()->create([
            'time' => '21:00:00',
            'timezone' => 'Asia/Tokyo',
        ]);

        // It should be due because 21:00 Tokyo = 12:00 UTC
        $result = $this->findDueSchedules();
        $this->assertCount(1, $result, 'Expected schedule due considering timezone conversion');
    }

    public function test_daily_schedule_already_executed_today_not_due()
    {
        // Set current time in UTC
        $now = now('UTC');
        Carbon::setTestNowAndTimezone($now, 'UTC');

        // Create schedule with explicit timezone UTC for simplicity
        $schedule = Schedule::factory()->daily()->create([
            'time' => $now->format('H:i:s'),
            'timezone' => 'UTC',
        ]);

        // Create TriggerExecution "today" in UTC, 10 minutes before now
        $trigger = TriggerExecution::factory()->create([
            'created_at' => $now->copy()->subMinutes(10),
            'updated_at' => $now->copy()->subMinutes(10),
            'status_code' => 'Finished',
            'run_reason_code' => 'Scheduled',
            'origin_type' => 'CRON',
            'origin_id' => '',
            'context' => json_encode([]),
        ]);
        $schedule->triggerExecutions()->save($trigger);

        $result = $this->findDueSchedules();
        $this->assertEmpty($result, 'Expected no due schedule because it already executed today');
    }

    public function test_weekly_schedule_with_sunday_as_zero()
    {
        // Sunday 14:00 UTC
        Carbon::setTestNowAndTimezone(Carbon::create(2025, 6, 22, 14, 0, 0), 'UTC');

        Schedule::factory()->weekly()->create([
            'days_of_the_week' => [0], // Sunday
            'time' => Carbon::getTestNow()->format('H:i:s'),
        ]);

        $result = $this->findDueSchedules();
        $this->assertCount(1, $result, 'Expected schedule due on Sunday');
    }

    public function test_once_schedule_exact_time_due()
    {
        Carbon::setTestNow(now());

        Schedule::factory()->once()->create([
            'one_time_at' => Carbon::getTestNow()->copy()->format('Y-m-d H:i:s'),
        ]);

        $result = $this->findDueSchedules();
        $this->assertCount(1, $result, 'Expected once schedule to be due exactly at current time');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNowAndTimezone();
    }

    private function findDueSchedules()
    {
        $service = new DueScheduleFinder;

        return $service->find();
    }
}
