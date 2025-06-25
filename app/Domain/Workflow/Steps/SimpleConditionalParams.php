<?php

namespace App\Domain\Workflow\Steps;

final class SimpleConditionalParams extends StepParams
{
    public function __construct()
    {
    }

    public static function from(array $data): self
    {
        $validated = self::validate($data, [
        ]);

        return new self();
    }
}