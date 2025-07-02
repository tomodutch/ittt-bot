<?php

namespace Tests\Feature\Workflow\Steps;

use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\StepExecutionContext;
use App\Domain\Workflow\StepResultBuilder;
use App\Domain\Workflow\Steps\SimpleConditional\SimpleConditionalStepHandler;
use App\Domain\Workflow\Steps\StepExecutionMetadata;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SimpleConditionalStepTest extends TestCase
{
    #[DataProvider('conditionalCases')]
    public function test_conditional_step(array $variables, array $params, string $expectedNextStepKey)
    {
        $step = new SimpleConditionalStepHandler;
        $context = new StepExecutionContext(collect($variables));
        $context = $context->withParams(collect($params))->WithMetadata(new StepExecutionMetadata('1', '2'));
        $builder = new StepResultBuilder;

        $step->process($context, $builder);
        $result = $builder->build();

        $directive = $result->getDirective();
        $this->assertInstanceOf(ContinueDirective::class, $directive);
        if ($directive instanceof ContinueDirective) {
            $this->assertEquals($expectedNextStepKey, $directive->nextStepKey);
        }
    }

    public static function conditionalCases(): array
    {
        return [
            'equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '==', 'right' => 5],
                '1',
            ],
            'equal (false)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '==', 'right' => 3],
                '2',
            ],
            'not equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '!=', 'right' => 3],
                '1',
            ],
            'not equal (false)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '!=', 'right' => 5],
                '2',
            ],
            'greater than (true)' => [
                ['value' => 10],
                ['left' => 'value', 'operator' => '>', 'right' => 5],
                '1',
            ],
            'greater than (false)' => [
                ['value' => 4],
                ['left' => 'value', 'operator' => '>', 'right' => 5],
                '2',
            ],
            'greater than or equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '>=', 'right' => 5],
                '1',
            ],
            'greater than or equal (false)' => [
                ['value' => 4],
                ['left' => 'value', 'operator' => '>=', 'right' => 5],
                '2',
            ],
            'less than (true)' => [
                ['value' => 3],
                ['left' => 'value', 'operator' => '<', 'right' => 5],
                '1',
            ],
            'less than (false)' => [
                ['value' => 6],
                ['left' => 'value', 'operator' => '<', 'right' => 5],
                '2',
            ],
            'less than or equal (true)' => [
                ['value' => 5],
                ['left' => 'value', 'operator' => '<=', 'right' => 5],
                '1',
            ],
            'less than or equal (false)' => [
                ['value' => 6],
                ['left' => 'value', 'operator' => '<=', 'right' => 5],
                '2',
            ],
            'exists (true)' => [
                ['foo' => 'bar'],
                ['left' => 'foo', 'operator' => 'exists'],
                '1',
            ],
            'exists (false)' => [
                [],
                ['left' => 'foo', 'operator' => 'exists'],
                '2',
            ],
            'not exists (true)' => [
                [],
                ['left' => 'foo', 'operator' => 'not_exists'],
                '1',
            ],
            'not exists (false)' => [
                ['foo' => 'bar'],
                ['left' => 'foo', 'operator' => 'not_exists'],
                '2',
            ],
            'null (true)' => [
                ['foo' => null],
                ['left' => 'foo', 'operator' => 'null'],
                '1',
            ],
            'null (false)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'null'],
                '2',
            ],
            'not null (true)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'not_null'],
                '1',
            ],
            'not null (false)' => [
                ['foo' => null],
                ['left' => 'foo', 'operator' => 'not_null'],
                '2',
            ],
            'empty (true)' => [
                ['foo' => ''],
                ['left' => 'foo', 'operator' => 'empty'],
                '1',
            ],
            'empty (false)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'empty'],
                '2',
            ],
            'not empty (true)' => [
                ['foo' => 'value'],
                ['left' => 'foo', 'operator' => 'not_empty'],
                '1',
            ],
            'not empty (false)' => [
                ['foo' => ''],
                ['left' => 'foo', 'operator' => 'not_empty'],
                '2',
            ],
            'contains (true)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'contains', 'right' => 'world'],
                '1',
            ],
            'contains (false)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'contains', 'right' => 'mars'],
                '2',
            ],
            'starts_with (true)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'starts_with', 'right' => 'hello'],
                '1',
            ],
            'starts_with (false)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'starts_with', 'right' => 'world'],
                '2',
            ],
            'ends_with (true)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'ends_with', 'right' => 'world'],
                '1',
            ],
            'ends_with (false)' => [
                ['foo' => 'hello world'],
                ['left' => 'foo', 'operator' => 'ends_with', 'right' => 'hello'],
                '2',
            ],
            'matches (true)' => [
                ['foo' => 'abc123'],
                ['left' => 'foo', 'operator' => 'matches', 'right' => "/^[a-z]+\d+$/"],
                '1',
            ],
            'matches (false)' => [
                ['foo' => '123abc'],
                ['left' => 'foo', 'operator' => 'matches', 'right' => "/^[a-z]+\d+$/"],
                '2',
            ],
            'in (true)' => [
                ['foo' => 'apple'],
                ['left' => 'foo', 'operator' => 'in', 'right' => ['apple', 'banana']],
                '1',
            ],
            'in (false)' => [
                ['foo' => 'orange'],
                ['left' => 'foo', 'operator' => 'in', 'right' => ['apple', 'banana']],
                '2',
            ],
            'not_in (true)' => [
                ['foo' => 'orange'],
                ['left' => 'foo', 'operator' => 'not_in', 'right' => ['apple', 'banana']],
                '1',
            ],
            'not_in (false)' => [
                ['foo' => 'apple'],
                ['left' => 'foo', 'operator' => 'not_in', 'right' => ['apple', 'banana']],
                '2',
            ],
        ];
    }
}
