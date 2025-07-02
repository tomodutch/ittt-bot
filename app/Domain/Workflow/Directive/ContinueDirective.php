<?php

namespace App\Domain\Workflow\Directive;

final class ContinueDirective extends FlowDirective
{
    public function __construct(public readonly ?string $nextStepKey) {}
}
