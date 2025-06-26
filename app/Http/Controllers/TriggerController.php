<?php

namespace App\Http\Controllers;

use App\Models\Trigger;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TriggerController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Creating a new trigger', [
            'request_data' => $request->all(),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'executionType' => 'required|integer|in:0,1,2',

            'schedules' => 'required|array|min:1',
            'schedules.*.typeCode' => 'required|integer|in:0,1,2',
            'schedules.*.oneTimeAt' => 'nullable|date',
            'schedules.*.runTime' => 'nullable|date_format:H:i',
            'schedules.*.daysOfWeek' => 'nullable|array',
            'schedules.*.daysOfWeek.*' => 'integer|min:0|max:6',
            'schedules.*.timezone' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->header('X-Inertia')) {
                throw new ValidationException($validator);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {
            $trigger = Trigger::create([
                'name' => $validated['name'],
                "user_id" => $request->user()->id,
                'description' => $validated['description'] ?? null,
                'execution_type' => $validated['executionType'],
            ]);

            foreach ($validated['schedules'] as $s) {
                $trigger->schedules()->create([
                    'type_code' => $s['typeCode'],
                    'one_time_at' => $s['typeCode'] === 0 ? $s['oneTimeAt'] : null,
                    'run_time' => $s['typeCode'] !== 0 ? $s['runTime'] : null,
                    'days_of_week' => $s['typeCode'] === 2 ? $s['daysOfWeek'] ?? [] : null,
                    'timezone' => $s['timezone'],
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
        $triggers = Trigger::with('schedules')->latest()->get();

        return inertia('triggers/index', [
            'triggers' => $triggers,
        ]);
    }

    public function show(Trigger $trigger)
    {
        $trigger->load('schedules')->load("steps");

        return inertia('triggers/show', [
            'trigger' => $trigger,
        ]);
    }

    public function create()
    {
        return inertia('triggers/create');
    }
}
