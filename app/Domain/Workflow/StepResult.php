<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Directive\FlowDirective;

final class StepResult
{
    public function __construct(
        private array $variables,
        private FlowDirective $directive,
        private array $logs
    ) {}

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getDirective(): FlowDirective
    {
        return $this->directive;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}