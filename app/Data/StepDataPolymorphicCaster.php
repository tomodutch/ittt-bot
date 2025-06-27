<?php

namespace App\Data;

use App\Domain\Workflow\Steps\StepType;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class StepDataPolymorphicCaster implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        $collect = collect();

        foreach ($value as $rawStep) {
            // Normalize enum if necessary
            $type = $rawStep['type'] instanceof StepType ? $rawStep['type'] : StepType::tryFrom($rawStep['type']);
            if (! $type) {
                throw new \InvalidArgumentException("Invalid step type: {$rawStep['type']}");
            }
            $class = $type->getDataClass();
            $newStep = $class::from($class::validate($rawStep));
            $collect->push($newStep);
        }

        return $collect;
    }
}
