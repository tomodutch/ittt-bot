<?php

namespace App\Services;

use App\Enums\ScheduleType;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DueScheduleFinder
{
    public function find(): Collection
    {
        $nowUtc = Carbon::now('UTC');
        Log::info('Finding due schedules');

        // Load schedules with latest execution to minimize queries
        $schedules = Schedule::with(['triggerExecutions' => function ($query) {
            $query->orderByDesc('trigger_executions.created_at')->limit(1);
        }])
            ->whereIn('type_code', [ScheduleType::Once, ScheduleType::Daily, ScheduleType::Weekly])
            ->get();

        $due = $schedules->filter(fn ($schedule) => $this->isScheduleDue($schedule, $nowUtc));

        Log::info('Done finding due schedules', [
            'total' => $due->count(),
            'once' => $due->where('type_code', ScheduleType::Once)->count(),
            'daily' => $due->where('type_code', ScheduleType::Daily)->count(),
            'weekly' => $due->where('type_code', ScheduleType::Weekly)->count(),
        ]);

        return $due;
    }

    protected function isScheduleDue(Schedule $schedule, Carbon $nowUtc): bool
    {
        $lastExecution = $schedule->triggerExecutions->first();
        $lastExecutedAt = $lastExecution ? Carbon::parse($lastExecution->created_at) : null;

        switch ($schedule->type_code) {
            case ScheduleType::Once:
                return $this->isOnceScheduleDue($schedule, $nowUtc, $lastExecutedAt);

            case ScheduleType::Daily:
                return $this->isDailyScheduleDue($schedule, $nowUtc, $lastExecutedAt);

            case ScheduleType::Weekly:
                return $this->isWeeklyScheduleDue($schedule, $nowUtc, $lastExecutedAt);

            default:
                return false;
        }
    }

    protected function isOnceScheduleDue(Schedule $schedule, Carbon $nowUtc, ?Carbon $lastExecutedAt): bool
    {
        if (! $schedule->one_time_at) {
            return false;
        }

        $oneTimeAtUtc = Carbon::parse($schedule->one_time_at)->setTimezone('UTC');

        // Due if one_time_at <= now and not executed before
        return $oneTimeAtUtc->lte($nowUtc) && $lastExecutedAt === null;
    }

    protected function isDailyScheduleDue(Schedule $schedule, Carbon $nowUtc, ?Carbon $lastExecutedAt): bool
    {
        if (! $schedule->time || ! $schedule->timezone) {
            return false;
        }

        [$hour, $minute] = explode(':', $schedule->time);

        // Current time in schedule's timezone
        $nowLocal = $nowUtc->copy()->setTimezone($schedule->timezone);

        // Scheduled run time today in schedule's timezone
        $scheduledRunToday = $nowLocal->copy()->setTime($hour, $minute, 0);

        // If scheduled time is in the future, not due yet
        if ($nowLocal->lt($scheduledRunToday)) {
            return false;
        }

        if (! $lastExecutedAt) {
            return true;
        }

        // Convert last execution time to schedule timezone
        $lastExecutedLocal = $lastExecutedAt->copy()->setTimezone($schedule->timezone);

        // If last executed on the same day as scheduled run time, not due
        if ($lastExecutedLocal->isSameDay($scheduledRunToday)) {
            return false;
        }

        return true;
    }

    protected function isWeeklyScheduleDue(Schedule $schedule, Carbon $nowUtc, ?Carbon $lastExecutedAt): bool
    {
        if (! $schedule->time || ! $schedule->timezone || empty($schedule->days_of_the_week)) {
            return false;
        }

        [$hour, $minute] = explode(':', $schedule->time);

        // Current time in schedule's timezone
        $nowLocal = $nowUtc->copy()->setTimezone($schedule->timezone);

        // Convert days 0=Sun..6=Sat to ISO (1=Mon..7=Sun) for easier comparison
        $scheduledDaysIso = array_map(fn ($d) => $d === 0 ? 7 : $d, $schedule->days_of_the_week);

        $todayIso = $nowLocal->dayOfWeekIso; // 1=Mon..7=Sun

        // If today is not a scheduled day, not due
        if (! in_array($todayIso, $scheduledDaysIso, true)) {
            return false;
        }

        // Scheduled run time today in schedule's timezone
        $scheduledRunToday = $nowLocal->copy()->setTime($hour, $minute, 0);

        // If scheduled time is in the future, not due
        if ($nowLocal->lt($scheduledRunToday)) {
            return false;
        }

        if (! $lastExecutedAt) {
            return true;
        }

        // Convert last execution time to schedule timezone for comparison
        $lastExecutedLocal = $lastExecutedAt->copy()->setTimezone($schedule->timezone);

        // If last executed on the same day as scheduled run time, not due
        if ($lastExecutedLocal->isSameDay($scheduledRunToday)) {
            return false;
        }

        return true;
    }
}
