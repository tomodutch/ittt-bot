<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Steps\StepExecutionMetadata;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class StepExecutionContext
{
    public function __construct(private Collection $variables, private Collection $params = new Collection, private ?StepExecutionMetadata $metadata = null) {}

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
        return new self($this->variables, $params, $this->metadata);
    }

    public function WithMetadata(StepExecutionMetadata $metadata): self
    {
        return new self($this->variables, $this->params, $metadata);
    }

    public function getParams(): Collection
    {
        return $this->params;
    }

    public function hasVariable(string $key): bool
    {
        return Arr::has($this->variables->all(), $key);
    }

    public function getNextStepKey()
    {
        if (! $this->metadata) {
            return null;
        }

        return $this->metadata->nextStepKey;
    }

    public function getNextStepKeyIfFalse()
    {
        if (! $this->metadata) {
            return null;
        }

        return $this->metadata->nextStepKeyIfFalse;
    }

    public function merge(Collection $newVariables): self
    {
        $mergedArray = array_replace_recursive($this->variables->all(), $newVariables->all());

        return new self(collect($mergedArray), $this->params, $this->metadata);
    }
}
