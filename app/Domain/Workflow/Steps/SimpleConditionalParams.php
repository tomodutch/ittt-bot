<?php

namespace App\Domain\Workflow\Steps;

final class SimpleConditionalParams extends StepParams
{
    public function __construct(private string $left, private string $operator, private mixed $right)
    {
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getRight()
    {
        return $this->right;
    }

    public static function from(array $data): self
    {
        $supportedOperators = [
            '==',
            '!=',
            '>',
            '>=',
            '<',
            '<=',
            'exists',
            'not_exists',
            'empty',
            'not_empty',
            'null',
            'not_null',
            'contains',
            'starts_with',
            'ends_with',
            'matches',
            'in',
            'not_in',
        ];

        $operator = $data['operator'] ?? null;

        if (!in_array($operator, $supportedOperators, true)) {
            throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        $rules = [
            'left' => ['required', 'string'],
            'operator' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($supportedOperators) {
                    if (!in_array($value, $supportedOperators, true)) {
                        $fail("The operator '$value' is not supported.");
                    }
                }
            ],
        ];

        $needsRight = in_array($operator, [
            '==',
            '!=',
            '>',
            '>=',
            '<',
            '<=',
            'contains',
            'starts_with',
            'ends_with',
            'matches',
            'in',
            'not_in',
        ], true);

        if ($needsRight) {
            $rules['right'] = ['required'];

            if (in_array($operator, ['>', '>=', '<', '<='], true)) {
                $rules['right'][] = function ($attribute, $value, $fail) use ($operator) {
                    if (!is_numeric($value)) {
                        $fail("The right-hand value must be numeric for operator '$operator'.");
                    }
                };
            }

            if (in_array($operator, ['in', 'not_in'], true)) {
                $rules['right'][] = 'array';
            }

            if ($operator === 'matches') {
                $rules['right'][] = function ($attribute, $value, $fail) {
                    set_error_handler(fn() => null);
                    $isValid = @preg_match($value, '') !== false;
                    restore_error_handler();
                    if (!$isValid) {
                        $fail("The right-hand value must be a valid regex pattern.");
                    }
                };
            }

            // For string operators like contains, starts_with, ends_with, just require string
            if (in_array($operator, ['contains', 'starts_with', 'ends_with'], true)) {
                $rules['right'][] = 'string';
            }
        } else {
            // For operators that don't require right param, allow right to be missing or null
            $rules['right'] = ['nullable'];
        }

        $validated = self::validate($data, $rules);

        return new self(
            $validated['left'],
            $validated['operator'],
            $validated['right'] ?? null
        );
    }
}