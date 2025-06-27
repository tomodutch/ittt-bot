<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Support\VariableInterpolator;

class VariableInterpolatorTest extends TestCase
{
    /**
     * @dataProvider interpolationCases
     */
    public function test_interpolation(string $template, array $context, string $expected)
    {
        $result = VariableInterpolator::interpolate($template, $context);
        $this->assertSame($expected, $result);
    }

    public static function interpolationCases(): array
    {
        return [
            'simple variable' => [
                'Hello {{ user.name }}!',
                ['user' => ['name' => 'Thomas']],
                'Hello Thomas!',
            ],
            'missing variable' => [
                'Missing: {{ user.email }}',
                ['user' => ['name' => 'Thomas']],
                'Missing: ',
            ],
            'multiple variables' => [
                'User: {{ user.name }}, ID: {{ user.id }}',
                ['user' => ['name' => 'Thomas', 'id' => 42]],
                'User: Thomas, ID: 42',
            ],
            'whitespace in placeholders' => [
                'Hello {{    user.name    }}!',
                ['user' => ['name' => 'Thomas']],
                'Hello Thomas!',
            ],
            'non-matching braces' => [
                'This is not a variable: { user.name } or {{user.name',
                ['user' => ['name' => 'Thomas']],
                'This is not a variable: { user.name } or {{user.name',
            ],
            'nested data' => [
                'City: {{ user.address.city }}',
                ['user' => ['address' => ['city' => 'Somewhere']]],
                'City: Somewhere',
            ],
            'non-string value' => [
                'Count: {{ items.count }}',
                ['items' => ['count' => 10]],
                'Count: 10',
            ],
            'no placeholders' => [
                'Static string',
                ['any' => 'data'],
                'Static string',
            ],
        ];
    }
}
