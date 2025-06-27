<?php

namespace App\Domain\Workflow;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class StepExecutionContext
{
    public function __construct(private Collection $variables, private Collection $params = new Collection())
    {
    }

    public function getVariable(string $key)
    {
        return Arr::get($this->variables->all(), $key, null);
    }

    public function getVariables(): Collection
    {
        return $this->variables;
    }

    public function withParams(Collection $params): self
    {
        return new self($this->variables, $params);
    }

    public function getParams(): Collection
    {
        return $this->params;
    }

    public function hasVariable(string $key): bool
    {
        return Arr::has($this->variables->all(), $key);
    }

    public function merge(Collection $newVariables): self
    {
        $mergedArray = array_replace_recursive($this->variables->all(), $newVariables->all());
        return new self(collect($mergedArray), $this->params);
    }
}
