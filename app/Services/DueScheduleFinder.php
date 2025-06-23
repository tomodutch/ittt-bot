<?php

namespace App\Services;

use App\Enums\ScheduleType;
use App\Models\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DueScheduleFinder
{
    public function find(): Collection
    {
        $nowUtc = now("UTC");
        Log::info("Finding due schedules");
        $due = collect();
        $onceSchedules = Schedule::where("type_code", ScheduleType::Once)
            ->where('one_time_at', '<=', $nowUtc)
            ->whereDoesntHave('triggerExecutions', function ($query) {
                $query->whereNotNull('id');
            })
            ->get();

        Log::debug("Found ({$onceSchedules->count()}) once schedules due", [
            "schedules_count" => $onceSchedules->count()
        ]);

        $dailySchedules = Schedule::where('type_code', ScheduleType::Daily)
            ->whereDoesntHave('triggerExecutions', fn($q) => $q->whereNotNull('id'))
            ->get()
            ->filter(function ($schedule) use ($nowUtc) {
                $localNow = $nowUtc->copy()->setTimezone($schedule->timezone);
                return $schedule->time <= $localNow->format('H:i:s');
            });

        Log::debug("Found ({$dailySchedules->count()}) daily schedules due", [
            "schedules_count" => $dailySchedules->count()
        ]);

        $weeklySchedules = Schedule::where('type_code', ScheduleType::Weekly)
            ->whereDoesntHave('triggerExecutions', fn($q) => $q->whereNotNull('id'))
            ->get()
            ->filter(function ($schedule) use ($nowUtc) {
                $localNow = $nowUtc->copy()->setTimezone($schedule->timezone);
                $weekday = $localNow->dayOfWeek === 0 ? 7 : $localNow->dayOfWeek;
                return in_array($weekday, $schedule->days_of_the_week ?? []) &&
                    $schedule->time <= $localNow->format('H:i:s');
            });

        Log::debug("Found ({$weeklySchedules->count()}) weekly schedules due", [
            "schedules_count" => $weeklySchedules->count()
        ]);

        $due = $due->merge($onceSchedules)->merge($dailySchedules)->merge($weeklySchedules);

        Log::info("Done finding due schedules", [
            "totalMilliseconds" => now("UTC")->diffInMilliseconds($nowUtc),
            "onceSchedules" => $onceSchedules->count(),
            "dailySchedules" => $dailySchedules->count(),
            "weeklySchedules" => $weeklySchedules->count(),
            "totalSchedules" => $due->count()
        ]);

        return $due;
    }
}