<?php

namespace App\Domain\Workflow;

use Illuminate\Support\Arr;

final class StepExecutionContext
{
    public function __construct(private array $variables, private array $params = [])
    {
    }

    public function getVariable(string $key)
    {
        return Arr::get($this->variables, $key, null);
    }

    public function getVariables()
    {
        return $this->variables;
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
        return Arr::has($this->variables, $key);
    }

    public function merge(array $newVariables): self
    {
        $merged = array_replace_recursive($this->variables, $newVariables);
        return new self($merged, $this->params);
    }
}
