<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;

final class SendEmailStepHandler implements StepHandlerContract
{
	public function process(StepExecutionContext $context, StepResultBuilder $builder): void
    {
        
	}
}