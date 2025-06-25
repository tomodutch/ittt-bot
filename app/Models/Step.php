<?php

namespace App\Models;

use App\Domain\Workflow\Steps\StepType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    /** @use HasFactory<\Database\Factories\StepFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        "id",
        "trigger_id",
        "type",
        "description",
        "order",
        "params"
    ];

    protected $casts = [
        'params' => 'array',
        "type" => StepType::class
    ];
}
