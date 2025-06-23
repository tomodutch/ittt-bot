<?php

namespace App\Services;

use App\Enums\ScheduleType;
use App\Models\Schedule;
use Illuminate\Support\Collection;

class DueScheduleFinder
{
    public function find(): Collection
    {
        $nowUtc = now("UTC");
        $due = collect();

        $nowTime = $nowUtc->format('H:i:s');
        $onceSchedules = Schedule::where("type_code", ScheduleType::Once)
            ->where('one_time_at', '<=', $nowUtc)
            ->whereDoesntHave('triggerExecutions', function ($query) {
                $query->whereNotNull('schedule_id');
            })
            ->get();

        $dailySchedules = Schedule::with('trigger')
            ->where('type_code', ScheduleType::Daily)
            ->whereDoesntHave('triggerExecutions', fn($q) => $q->whereNotNull('schedule_id'))
            ->get()
            ->filter(function ($schedule) use ($nowUtc) {
                $localNow = $nowUtc->copy()->setTimezone($schedule->timezone);
                return $schedule->time <= $localNow->format('H:i:s');
            });

        $weeklySchedules = Schedule::with('trigger')
            ->where('type_code', ScheduleType::Weekly)
            ->whereDoesntHave('triggerExecutions', fn($q) => $q->whereNotNull('schedule_id'))
            ->get()
            ->filter(function ($schedule) use ($nowUtc) {
                $localNow = $nowUtc->copy()->setTimezone($schedule->timezone);
                $weekday = $localNow->dayOfWeek === 0 ? 7 : $localNow->dayOfWeek; // 1=Mon ... 7=Sun
                return in_array($weekday, $schedule->days_of_the_week ?? []) &&
                    $schedule->time <= $localNow->format('H:i:s');
            });

        return $due->merge($onceSchedules)->merge($dailySchedules)->merge($weeklySchedules);
    }
}