<?php

namespace App\Domain\Workflow\Steps;

use Illuminate\Support\Facades\Validator;

abstract class StepParams
{
    public static function validate(array $data, array $rules): array
    {
        return Validator::make($data, $rules)->validate();
    }
}