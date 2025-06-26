<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ScheduleType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        "id",
        "trigger_id",
        "type_code",
        "one_time_at",
        "time",
        "days_of_the_week",
        "timezone"
    ];

    protected $casts = [
        "one_time_at" => "datetime",
        "time" => "string",
        "days_of_the_week" => "array",
        "type_code" => ScheduleType::class,
    ];

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(Trigger::class);
    }

    public function triggerExecutions()
    {
        return $this->belongsToMany(TriggerExecution::class, 'schedule_trigger_execution');
    }
}
