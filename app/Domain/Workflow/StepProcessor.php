<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Contracts\StepHandlerResolverContract;
use App\Domain\Workflow\Contracts\StepProcessorContract;
use App\Domain\Workflow\Directive\AbortDirective;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResult;
use App\Models\Step;
use Illuminate\Validation\ValidationException;

class StepProcessor implements StepProcessorContract
{
    public function __construct(private StepHandlerResolverContract $resolver)
    {
    }

    public function process(Step $step, StepExecutionContext $context): StepResult
    {
        $builder = new StepResultBuilder();
        $scopedContext = $context->withParams(collect($step->params));
        try {
            $step->type->getParamsClass()::from($step->params);
            $handler = $this->resolver->resolve($step);
            $handler->process($scopedContext, $builder);
        } catch (ValidationException $e) {
            $builder->error("Validation error", ["details" => $e->errors()]);
            $builder->setDirective(new AbortDirective());
        }

        return $builder->build();
    }
}