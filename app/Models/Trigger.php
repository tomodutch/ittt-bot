<?php

namespace App\Models;

use App\Enums\ExecutionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trigger extends Model
{
    /** @use HasFactory<\Database\Factories\TriggerFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        "id",
        "user_id",
        "name",
        "description",
        "execution_type",
    ];

    protected $casts = [
        "execution_type" => ExecutionType::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }

    public function executions()
    {
        return $this->hasMany(TriggerExecution::class);
    }
}
