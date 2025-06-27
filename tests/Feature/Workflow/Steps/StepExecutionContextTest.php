<?php

namespace Tests\Feature\Workflow\Steps;

use App\Domain\Workflow\StepExecutionContext;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StepExecutionContextTest extends TestCase
{
    #[DataProvider('getVariableProvider')]
    public function test_get_variable(array $variables, string $key, mixed $expected): void
    {
        $context = new StepExecutionContext(collect($variables));
        $this->assertSame($expected, $context->getVariable($key));
    }

    public static function getVariableProvider(): array
    {
        return [
            'nested existing key' => [
                ['user' => ['profile' => ['name' => 'Alice']]],
                'user.profile.name',
                'Alice',
            ],
            'nested missing key' => [
                ['user' => ['profile' => ['name' => 'Alice']]],
                'user.profile.age',
                null,
            ],
            'top-level existing key' => [
                ['foo' => 'bar'],
                'foo',
                'bar',
            ],
            'missing key' => [
                ['foo' => 'bar'],
                'baz',
                null,
            ],
        ];
    }

    #[DataProvider('hasVariableProvider')]
    public function test_has_variable(array $variables, string $key, bool $expected): void
    {
        $context = new StepExecutionContext(collect($variables));
        $this->assertSame($expected, $context->hasVariable($key));
    }

    public static function hasVariableProvider(): array
    {
        return [
            'nested existing key' => [
                ['foo' => ['bar' => 123]],
                'foo.bar',
                true,
            ],
            'top-level existing key' => [
                ['foo' => 'baz'],
                'foo',
                true,
            ],
            'missing nested key' => [
                ['foo' => ['bar' => 123]],
                'foo.baz',
                false,
            ],
            'missing key' => [
                ['foo' => 'bar'],
                'baz',
                false,
            ],
        ];
    }

    public function test_merge_creates_new_context_and_merges_variables(): void
    {
        $variables = [
            'foo' => ['bar' => 1, 'baz' => 2],
            'simple' => 'value1',
        ];
        $context = new StepExecutionContext(collect($variables));

        $newVariables = [
            'foo' => ['baz' => 20, 'newKey' => 30],
            'simple' => 'value2',
            'added' => 'newValue',
        ];

        $mergedContext = $context->merge(collect($newVariables));

        $this->assertSame(2, $context->getVariable('foo.baz'));
        $this->assertNull($context->getVariable('added'));

        $this->assertSame(20, $mergedContext->getVariable('foo.baz'));
        $this->assertSame('newValue', $mergedContext->getVariable('added'));
    }

    public function test_with_params_returns_new_context_with_updated_params(): void
    {
        $variables = ['key' => 'value'];
        $params = ['param1' => 'value1'];

        $context = new StepExecutionContext(collect($variables), collect($params));
        $newParams = ['param2' => 'value2'];
        $newContext = $context->withParams(collect($newParams));

        $this->assertEqualsCanonicalizing($params, $context->getParams()->toArray());
        $this->assertEqualsCanonicalizing($newParams, $newContext->getParams()->toArray());
        $this->assertEqualsCanonicalizing($variables, $newContext->getVariables()->toArray());

        $this->assertNotSame($context, $newContext);
    }
}
