<?php

namespace App\Domain\Workflow\Steps;

final class SendEmailParams extends StepParams
{
    public function __construct(private string $to, private string $message)
    {
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public static function from(array $data): self
    {
        $validated = self::validate($data, [
            'to' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        return new self($validated['to'], $validated["message"]);
    }
}