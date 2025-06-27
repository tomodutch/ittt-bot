<?php

namespace App\Models;

use App\Enums\LogLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StepExecutionLog extends Model
{
    /** @use HasFactory<\Database\Factories\StepExecutionLogFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'trigger_execution_id',
        'step_id',
        'level',
        'message',
        'details',
    ];

    protected $casts = [
        'level' => LogLevel::class,
        'details' => 'array',
    ];
}
