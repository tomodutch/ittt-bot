<?php

namespace App\Domain\Workflow\Steps\SimpleConditional;

use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\Directive\AbortDirective;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;
use App\Enums\Operator;

final class SimpleConditionalStepHandler implements StepHandlerContract
{
    public function process(StepExecutionContext $context, StepResultBuilder $builder): void
    {
        $params = SimpleConditionalStepParams::from($context->getParams());
        $leftValue = $context->getVariable($params->left);
        $rightValue = $params->right;
        $operator = $params->operator;

        $builder->info("Evaluating condition:", [
            "left" => $leftValue,
            "operator" => $operator->value,
            "right" => $rightValue
        ]);

        $result = match ($operator) {
            Operator::EQ => $leftValue == $rightValue,
            Operator::NEQ => $leftValue != $rightValue,
            Operator::GT => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue > $rightValue,
            Operator::GTE => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue >= $rightValue,
            Operator::LT => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue < $rightValue,
            Operator::LTE => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue <= $rightValue,

            Operator::EXISTS => $context->hasVariable($params->left),
            Operator::NOT_EXISTS => !$context->hasVariable($params->left),

            Operator::NULL => $leftValue === null,
            Operator::NOT_NULL => $leftValue !== null,

            Operator::EMPTY => empty($leftValue),
            Operator::NOT_EMPTY => !empty($leftValue),

            Operator::CONTAINS => is_string($leftValue) && str_contains((string) $leftValue, (string) $rightValue),
            Operator::STARTS_WITH => is_string($leftValue) && str_starts_with((string) $leftValue, (string) $rightValue),
            Operator::ENDS_WITH => is_string($leftValue) && str_ends_with((string) $leftValue, (string) $rightValue),

            Operator::MATCHES => is_string($leftValue) && @preg_match($rightValue, $leftValue) === 1,

            Operator::IN => is_array($rightValue) && in_array($leftValue, $rightValue, true),
            Operator::NOT_IN => is_array($rightValue) && !in_array($leftValue, $rightValue, true),
        };


        if ($result) {
            $builder->info("Condition evaluated to true", [
                "left" => $leftValue,
                "operator" => $operator->value,
                "right" => $rightValue
            ]);
            $builder->info("Continuing workflow execution");
            $builder->setDirective(new ContinueDirective());
        } else {
            $builder->info("Condition evaluated to false", [
                "left" => $leftValue,
                "operator" => $operator->value,
                "right" => $rightValue
            ]);
            $builder->info("Aborting workflow execution");
            $builder->setDirective(new AbortDirective());
        }
    }
}
