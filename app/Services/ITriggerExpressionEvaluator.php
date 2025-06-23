<?php

namespace App\Services;

use App\Models\Trigger;

interface ITriggerExpressionEvaluator
{
    /**
     * Summary of evaluate
     * @param \App\Models\Trigger $trigger
     * @param array $context
     * @return bool
     */
    public function evaluate(Trigger $trigger, array $context): bool;
}