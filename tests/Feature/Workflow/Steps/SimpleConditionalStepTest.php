<?php

namespace Tests\Feature\Workflow\Steps;

use App\Domain\Workflow\Directive\AbortDirective;
use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SimpleConditionalStepTest extends TestCase
{
    #[DataProvider('conditionalCases')]
    public function test_conditional_step(array $variables, array $params, string $expectedDirectiveClass)
    {
        $step = new SimpleConditionalStepHandler;
        $context = new StepExecutionContext(collect($variables), collect($params));
        $builder = new StepResultBuilder;

        $step->process($context, $builder);
        $result = $builder->build();

        $this->assertInstanceOf($expectedDirectiveClass, $result->getDirective());
    }

    public static function conditionalCases(): array
    {
        return [
            // Equality and comparison
            'equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '==', 'right' => 5],
                ContinueDirective::class,
            ],
            'equal (false)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '==', 'right' => 3],
                AbortDirective::class,
            ],
            'not equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '!=', 'right' => 3],
                ContinueDirective::class,
            ],
            'not equal (false)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '!=', 'right' => 5],
                AbortDirective::class,
            ],
            'greater than (true)' => [
                ['value' => 10],
                ['left' => 'value', 'operator' => '>', 'right' => 5],
                ContinueDirective::class,
            ],
            'greater than (false)' => [
                ['value' => 4],
                ['left' => 'value', 'operator' => '>', 'right' => 5],
                AbortDirective::class,
            ],
            'greater than or equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '>=', 'right' => 5],
                ContinueDirective::class,
            ],
            'greater than or equal (false)' => [
                ['value' => 4],
                ['left' => 'value', 'operator' => '>=', 'right' => 5],
                AbortDirective::class,
            ],
            'less than (true)' => [
                ['value' => 3],
                ['left' => 'value', 'operator' => '<', 'right' => 5],
                ContinueDirective::class,
            ],
            'less than (false)' => [
                ['value' => 6],
                ['left' => 'value', 'operator' => '<', 'right' => 5],
                AbortDirective::class,
            ],
            'less than or equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '<=', 'right' => 5],
                ContinueDirective::class,
            ],
            'less than or equal (false)' => [
                ['value' => 6],
                ['left' => 'value', 'operator' => '<=', 'right' => 5],
                AbortDirective::class,
            ],

            // Existence
            'exists (true)' => [
                ['foo' => 'bar'],
                ['left' => 'foo', 'operator' => 'exists'],
                ContinueDirective::class,
            ],
            'exists (false)' => [
                [],
                ['left' => 'foo', 'operator' => 'exists'],
                AbortDirective::class,
            ],
            'not exists (true)' => [
                [],
                ['left' => 'foo', 'operator' => 'not_exists'],
                ContinueDirective::class,
            ],
            'not exists (false)' => [
                ['foo' => 'bar'],
                ['left' => 'foo', 'operator' => 'not_exists'],
                AbortDirective::class,
            ],

            // Null checks
            'null (true)' => [
                ['foo' => null],
                ['left' => 'foo', 'operator' => 'null'],
                ContinueDirective::class,
            ],
            'null (false)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'null'],
                AbortDirective::class,
            ],
            'not null (true)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'not_null'],
                ContinueDirective::class,
            ],
            'not null (false)' => [
                ['foo' => null],
                ['left' => 'foo', 'operator' => 'not_null'],
                AbortDirective::class,
            ],

            // Empty checks
            'empty (true)' => [
                ['foo' => ''],
                ['left' => 'foo', 'operator' => 'empty'],
                ContinueDirective::class,
            ],
            'empty (false)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'empty'],
                AbortDirective::class,
            ],
            'not empty (true)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'not_empty'],
                ContinueDirective::class,
            ],
            'not empty (false)' => [
                ['foo' => ''],
                ['left' => 'foo', 'operator' => 'not_empty'],
                AbortDirective::class,
            ],

            // String operators
            'contains (true)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'contains', 'right' => 'world'],
                ContinueDirective::class,
            ],
            'contains (false)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'contains', 'right' => 'mars'],
                AbortDirective::class,
            ],
            'starts_with (true)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'starts_with', 'right' => 'hello'],
                ContinueDirective::class,
            ],
            'starts_with (false)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'starts_with', 'right' => 'world'],
                AbortDirective::class,
            ],
            'ends_with (true)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'ends_with', 'right' => 'world'],
                ContinueDirective::class,
            ],
            'ends_with (false)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'ends_with', 'right' => 'hello'],
                AbortDirective::class,
            ],

            // Regex match
            'matches (true)' => [
                ['foo' => 'abc123'],
                ['left' => 'foo', 'operator' => 'matches', 'right' => "/^[a-z]+\d+$/"],
                ContinueDirective::class,
            ],
            'matches (false)' => [
                ['foo' => '123abc'],
                ['left' => 'foo', 'operator' => 'matches', 'right' => "/^[a-z]+\d+$/"],
                AbortDirective::class,
            ],

            // Array membership
            'in (true)' => [
                ['foo' => 'apple'],
                ['left' => 'foo', 'operator' => 'in', 'right' => ['apple', 'banana']],
                ContinueDirective::class,
            ],
            'in (false)' => [
                ['foo' => 'orange'],
                ['left' => 'foo', 'operator' => 'in', 'right' => ['apple', 'banana']],
                AbortDirective::class,
            ],
            'not_in (true)' => [
                ['foo' => 'orange'],
                ['left' => 'foo', 'operator' => 'not_in', 'right' => ['apple', 'banana']],
                ContinueDirective::class,
            ],
            'not_in (false)' => [
                ['foo' => 'apple'],
                ['left' => 'foo', 'operator' => 'not_in', 'right' => ['apple', 'banana']],
                AbortDirective::class,
            ],
        ];
    }
}
