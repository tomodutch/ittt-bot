<?php

namespace App\Http\Controllers;

use App\Data\TriggerData;
use App\Domain\Workflow\Steps\StepType;
use App\Enums\ScheduleType;
use App\Models\Trigger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TriggerController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate and hydrate DTO from request input
            TriggerData::validate($request->all());
        } catch (ValidationException $e) {
            // When validation fails, Inertia expects a ValidationException with errors
            if ($request->header('X-Inertia')) {
                throw new ValidationException($e->validator);
            }

            // For non-Inertia fallback
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        $triggerData = TriggerData::from($request->all());
        return DB::transaction(function () use ($triggerData, $request) {
            $trigger = Trigger::create([
                'name' => $triggerData->name,
                "user_id" => $request->user()->id,
                'description' => $triggerData->description,
                'execution_type' => $triggerData->executionType,
            ]);

            foreach ($triggerData->schedules as $s) {
                $timezone = $s->timezone ?? 'UTC';

                $trigger->schedules()->create([
                    'type_code' => $s->typeCode,
                    'one_time_at' => $s->typeCode === ScheduleType::Once && $s->oneTimeAt
                        ? Carbon::parse($s->oneTimeAt, $timezone)->utc()
                        : null,
                    'time' => $s->typeCode !== ScheduleType::Once && $s->time
                        ? Carbon::parse($s->time, $timezone)->utc()->format('H:i')
                        : null,
                    'days_of_week' => $s->typeCode === ScheduleType::Weekly ? $s->daysOfWeek ?? [] : null,
                    'timezone' => $timezone,
                ]);
            }

            foreach ($triggerData->steps as $step) {
                $trigger->steps()->create([
                    'type' => $step->type,
                    'name' => "test",
                    "order" => $step->order,
                    'description' => $step->description,
                    "params" => $step->params
                ]);
            }

            return redirect()->route('triggers.show', $trigger->id)
                ->with('success', 'Trigger created successfully!');
        });
    }

    public function update(Request $request, Trigger $trigger)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'executionType' => 'required|integer|in:0,1,2',
            'timezone' => 'required|string',
        ]);

        $trigger->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'execution_type' => $validated['executionType'],
            'timezone' => $validated['timezone'],
        ]);

        return redirect()->route('triggers.edit', $trigger->id)
            ->with('success', 'Trigger updated.');
    }

    public function destroy(Trigger $trigger)
    {
        $trigger->delete();
        return redirect()->route('triggers.index')->with('success', 'Trigger deleted.');
    }

    public function index()
    {
        $triggers = TriggerData::collect(Trigger::with(['schedules', "steps"])->latest()->get());

        return inertia('triggers/index', [
            'triggers' => $triggers,
        ]);
    }

    public function show(Trigger $trigger)
    {
        $triggerData = TriggerData::from(
            $trigger->load('schedules')
            ->load("executions")
            ->load("steps"));

        return inertia('triggers/show', [
            'trigger' => $triggerData,
        ]);
    }

    public function create()
    {
        return inertia('triggers/create');
    }
}
