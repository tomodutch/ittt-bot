<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\Contracts\StepHandlerResolverContract;
use App\Models\Step;
use RuntimeException;

class StepHandlerResolver implements StepHandlerResolverContract
{
    public function resolve(Step $step): StepHandlerContract
    {
        $handlerClass = $step->type->handlerClass();
        // create an instance of the step handler
        return app($handlerClass);
    }
}
