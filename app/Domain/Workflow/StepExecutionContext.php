<?php

namespace App\Domain\Workflow;

final class StepExecutionContext
{
    public function __construct(private array $variables, private array $params = [])
    {
    }

    public function getVariable(string $key)
    {
        if ($this->hasVariable($key)) {
            return $this->variables[$key];
        }

        return null;
    }

    public function withParams(array $params)
    {
        return new self($this->variables, $params);
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function hasVariable(string $key)
    {
        return array_key_exists($key, $this->variables);
    }

    public function merge(array $variables)
    {
        return new self(array_merge($this->variables, $variables));
    }
}
