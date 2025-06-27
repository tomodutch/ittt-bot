<?php

namespace Tests\Feature\Workflow\Steps;

use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;
use App\Domain\Workflow\Steps\SendEmail\SendEmailStepHandler;
use App\Mail\StepMailable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendEmailStepTest extends TestCase
{
    public function test_no_cc()
    {
        Mail::fake();

        $params = collect([
            'to' => 'john@example.com',
            'cc' => null,
            'bcc' => null,
            'subject' => 'Test Subject',
            'body' => 'Hello from the test!',
        ]);

        $context = new StepExecutionContext(collect(), $params);

        $builder = new StepResultBuilder;
        $handler = new SendEmailStepHandler;
        $handler->process($context, $builder);

        Mail::assertSent(StepMailable::class, function (StepMailable $mail) use ($params) {
            return $mail->hasTo($params['to']);
        });
    }

    public function test_send()
    {
        Mail::fake();

        $params = collect([
            'to' => 'john@example.com',
            'cc' => 'carol@example.com',
            'bcc' => 'dave@example.com',
            'subject' => 'Test Subject',
            'body' => 'Hello from the test!',
        ]);

        $context = new StepExecutionContext(collect(), $params);

        $builder = new StepResultBuilder;
        $handler = new SendEmailStepHandler;
        $handler->process($context, $builder);

        Mail::assertSent(StepMailable::class, function (StepMailable $mail) use ($params) {
            return
                $mail->hasTo($params['to']) &&
                $mail->hasCc($params['cc']) &&
                $mail->hasBcc($params['bcc']) &&
                $mail->hasSubject($params['subject']);
        });
    }
}
