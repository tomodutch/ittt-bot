<?php

namespace App\Services;

use App\Models\Trigger;
use Illuminate\Support\Collection;

class TriggerExpressionEvaluator implements ITriggerExpressionEvaluator
{
    /**
     * Summary of evaluate
     * @param \App\Models\Trigger $trigger
     * @param array $context
     * @return bool
     */
    public function evaluate(Trigger $trigger, array $context): bool
    {
        return false;
    }
}