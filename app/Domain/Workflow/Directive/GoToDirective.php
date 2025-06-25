<?php

namespace App\Domain\Workflow\Directive;

final class GoToDirective extends FlowDirective
{
    public function __construct(private string $stepId) {}
    public function getStepId(): string {
        return $this->stepId;
    }
}