<?php
namespace App\Domain\Workflow\Steps\SimpleConditional;

use App\Enums\Operator;
use Spatie\LaravelData\Data;

final class SimpleConditionalStepParams extends Data
{
    public function __construct(public readonly string $left, public readonly Operator $operator, public readonly mixed $right)
    {
    }
}
