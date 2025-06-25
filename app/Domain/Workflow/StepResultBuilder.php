<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Directive\ContinueDirective;
use App\Domain\Workflow\Directive\FlowDirective;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class StepResultBuilder implements LoggerInterface
{
    private array $variables = [];
    private ?FlowDirective $directive = null;
    private array $logs = [];

    public function setVariable(string $key, mixed $value): self
    {
        Arr::set($this->variables, $key, $value);
        return $this;
    }

    public function setDirective(FlowDirective $flowDirective): self
    {
        $this->directive = $flowDirective;
        return $this;
    }

    public function build(): StepResult
    {
        return new StepResult(
            variables: $this->variables,
            directive: $this->directive ?? new ContinueDirective(),
            logs: $this->logs
        );
    }

    public function log($level, $message, array $context = []): void
    {
        $this->logs[] = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ];
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}