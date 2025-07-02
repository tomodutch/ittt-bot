<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;
use App\Mail\StepMailable;
use App\Support\VariableInterpolator;
use Illuminate\Support\Facades\Mail;

final class SendEmailStepHandler implements StepHandlerContract
{
    public function process(StepExecutionContext $context, StepResultBuilder $builder): void
    {
        $variables = $context->getVariables();
        $params = $context->getParams();
        $to = $params->get('to');
        $bcc = $params->get('bcc');
        $cc = $params->get('cc');
        $subject = VariableInterpolator::interpolate($params->get('subject'), $variables);
        $body = VariableInterpolator::interpolate($params->get('body'), $variables);

        $builder->info("Sending email to \"{$to}\"");
        $mailBuilder = Mail::to($to);
        if ($cc) {
            $mailBuilder = $mailBuilder->cc($cc);
        }
        if ($bcc) {
            $mailBuilder = $mailBuilder->bcc($bcc);
        }

        $mailBuilder->send(new StepMailable(
            $subject,
            $body
        ));

        $builder->info('Email sent successfully', [
            'to' => $context->getParams()->get('to'),
            'subject' => $context->getParams()->get('subject'),
        ]);

        $builder->setDirective(new ContinueDirective($context->getNextStepKey()));
    }
}
