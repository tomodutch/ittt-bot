<?php

namespace App\Http\Controllers;

use App\Models\TriggerExecution;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Data\TriggerExecutionData; // Your data transformer, adjust as needed

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
