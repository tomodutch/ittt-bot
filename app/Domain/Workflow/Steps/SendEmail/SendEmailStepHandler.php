<?php

namespace App\Domain\Workflow\Steps\SendEmail;

use App\Domain\Workflow\Contracts\StepHandlerContract;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;
use App\Mail\StepMailable;
use Illuminate\Support\Facades\Mail;

final class SendEmailStepHandler implements StepHandlerContract
{
	public function process(StepExecutionContext $context, StepResultBuilder $builder): void
	{
		$params = $context->getParams();
		$to = $params->get("to");
		$bcc = $params->get("bcc");
		$cc = $params->get("cc");
		$subject = $params->get("subject");
		$body = $params->get("body");

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

		$builder->info("Email sent successfully", [
			"to" => $context->getParams()->get('to'),
			"subject" => $context->getParams()->get('subject'),
		]);
	}
}