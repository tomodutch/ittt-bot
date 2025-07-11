<?php

namespace App\Models;

use App\Enums\ExecutionStatus;
use App\Enums\RunReason;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriggerExecution extends Model
{
    /** @use HasFactory<\Database\Factories\TriggerExecutionFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'trigger_id',
        'origin_type',
        'origin_id',
        'status_code',
        'run_reason_code',
        'context',
        'finished_at',
    ];

    protected $casts = [
        'context' => 'array',
        'status_code' => ExecutionStatus::class,
        'run_reason_code' => RunReason::class,
    ];

    public function trigger()
    {
        return $this->belongsTo(Trigger::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_trigger_execution');
    }

    public function logs()
    {
        return $this->hasMany(StepExecutionLog::class, 'trigger_execution_id');
    }
}
