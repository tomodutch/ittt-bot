<?php

namespace App\Domain\Workflow\Contracts;

use App\Models\Step;

interface StepHandlerResolverContract
{
    public function resolve(Step $step): StepHandlerContract;
}