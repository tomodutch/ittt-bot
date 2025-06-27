<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Directive\FlowDirective;
use Illuminate\Support\Collection;

final class StepResult
{
    public function __construct(
        private Collection $variables,
        private FlowDirective $directive,
        private Collection $logs
    ) {}

    public function getVariables(): Collection
    {
        return $this->variables;
    }

    public function getDirective(): FlowDirective
    {
        return $this->directive;
    }

    public function getLogs(): Collection
    {
        return $this->logs;
    }
}