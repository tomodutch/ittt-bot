<?php

namespace App\Domain\Workflow\Steps;

use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\Directive\AbortDirective;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;

final class SimpleConditionalStep implements StepHandlerContract
{
	public function process(StepExecutionContext $context, StepResultBuilder $builder): void
	{
		$params = SimpleConditionalParams::from($context->getParams());

		$leftValue = $context->getVariable($params->getLeft());
		$rightValue = $params->getRight();
		$operator = $params->getOperator();

		$result = match ($operator) {
			// Equality and comparison
			'==' => $leftValue == $rightValue,
			'!=' => $leftValue != $rightValue,
			'>' => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue > $rightValue,
			'>=' => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue >= $rightValue,
			'<' => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue < $rightValue,
			'<=' => is_numeric($leftValue) && is_numeric($rightValue) && $leftValue <= $rightValue,

			// Existence
			'exists' => $context->hasVariable($params->getLeft()),
			'not_exists' => !$context->hasVariable($params->getLeft()),

			// Null checks
			'null' => $leftValue === null,
			'not_null' => $leftValue !== null,

			// Empty checks
			'empty' => empty($leftValue),
			'not_empty' => !empty($leftValue),

			// String operations (convert to string for safety)
			'contains' => is_string($leftValue) && str_contains((string) $leftValue, (string) $rightValue),
			'starts_with' => is_string($leftValue) && str_starts_with((string) $leftValue, (string) $rightValue),
			'ends_with' => is_string($leftValue) && str_ends_with((string) $leftValue, (string) $rightValue),

			// Regex match
			'matches' => is_string($leftValue) && @preg_match($rightValue, $leftValue) === 1,

			// Array membership
			'in' => is_array($rightValue) && in_array($leftValue, $rightValue, true),
			'not_in' => is_array($rightValue) && !in_array($leftValue, $rightValue, true),

			default => throw new \InvalidArgumentException("Unsupported operator: $operator"),
		};

		if ($result) {
			$builder->setDirective(new ContinueDirective());
		} else {
			$builder->setDirective(new AbortDirective());
		}
	}
}
