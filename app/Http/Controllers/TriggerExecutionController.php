<?php

namespace App\Http\Controllers;

use App\Data\TriggerExecutionData;
use App\Models\TriggerExecution;
use Inertia\Inertia; // Your data transformer, adjust as needed

class TriggerExecutionController extends Controller
{
    public function show(string $triggerId, string $triggerExecutionId)
    {
        $execution = TriggerExecution::with(['logs', 'trigger', 'trigger.steps'])->findOrFail($triggerExecutionId);

        $executionData = TriggerExecutionData::from($execution);

        return Inertia::render('executions/show', [
            'execution' => $executionData,
        ]);
    }
}
