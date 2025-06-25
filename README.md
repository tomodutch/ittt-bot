# 🤖 if-this-then-that-bot

A lightweight Laravel-based automation bot that checks external APIs, evaluates conditions, and performs actions such as sending emails or SMS messages.

Inspired by tools like Zapier, IFTTT, and n8n. Built with Laravel + Livewire, this MVP allows you to create your own scripted workflows powered by cron jobs and conditional logic.

---

## ✨ Features

- ⏰ Poll external APIs on a schedule (e.g., daily)
- 🔎 Evaluate logic conditions on JSON responses
- 💬 Trigger actions: send email, send SMS, or notify elsewhere
- 🧩 Modular: add new data sources, conditions, and outputs easily

---

## 📦 Tech Stack

- [Laravel 11+](https://laravel.com) – Framework
- [Livewire](https://livewire.laravel.com) – For future UI
- Laravel Scheduler – Polls data sources
- Laravel Queues – Handles outbound jobs
- Laravel Mail / Notification – Output methods
- Twilio (or any SMS API) – Mobile alerts

---

## 📸 Example Use Case

> **“If it's cloudy tomorrow, send me a reminder email to bring an umbrella.”**

1. Fetch weather from API
2. Check if `response.weather.cloudy === true`
3. Send email if true

---

## Class Diagram

```mermaid
classDiagram
    class Trigger {
        UUID id
        string name
        string? description
        int executionType
        string timezone
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    class Schedule {
        UUID id
        UUID triggerId
        int typeCode
        datetime? oneTimeAt
        time? runTime
        smallint[]? daysOfWeek
        string timezone
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    class TriggerExecution {
        UUID id
        UUID triggerId
        UUID scheduleId
        string? originType
        string? originId
        int statusCode
        int runReasonCode
        jsonb context
        datetime finishedAt
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    class Step {
        UUID id
        UUID triggerId
        string? description
        int order
        jsonb? params
        string? actionName
        jsonb? actionParams
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    class StepExecutionLog {
        UUID id
        UUID triggerExecutionId
        UUID stepId
        string level
        string message
        jsonb details
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    class StepExecutionContext {
        +getVariable(string key): mixed
        +merge(array newVariables): StepExecutionContext
    }

    class StepResultBuilder {
        +setVariable(string key, mixed value): self
        +setDirective(FlowDirective): self
        +build(): StepResult
        +log(level, msg, ctx): void
    }

    class StepResult {
        +getVariables(): array
        +getDirective(): FlowDirective
        +getLogs(): array
    }

    class TriggerExecutionProcessor {
        +process(triggerExecution: TriggerExecution): void
    }

    class StepProcessor {
        +process(step: Step, context: StepExecutionContext): StepResult
    }

    class StepHandlerResolver {
        +resolve(step: Step): StepHandlerContract
    }

    class StepHandlerContract {
        <<interface>>
        +process(context: StepExecutionContext, builder: StepResultBuilder): void
    }

    class LoggerInterface {
        <<interface>>
        +emergency(msg, ctx): void
        +alert(msg, ctx): void
        +critical(msg, ctx): void
        +error(msg, ctx): void
        +warning(msg, ctx): void
        +notice(msg, ctx): void
        +info(msg, ctx): void
        +debug(msg, ctx): void
        +log(level, msg, ctx): void
    }

    %% FlowDirective hierarchy
    class FlowDirective {
        <<abstract>>
    }

    class ContinueDirective
    class AbortDirective
    class RetryDirective
    class SkipDirective
    class GotoStepDirective {
        string stepId
    }

    FlowDirective <|-- ContinueDirective
    FlowDirective <|-- AbortDirective
    FlowDirective <|-- RetryDirective
    FlowDirective <|-- SkipDirective
    FlowDirective <|-- GotoStepDirective

    Trigger "1" --> "many" Schedule : has
    Trigger "1" --> "many" Step : has
    Trigger "1" --> "many" TriggerExecution : runs
    TriggerExecution "1" --> "many" StepExecutionLog : logs
    Step "1" --> "many" StepExecutionLog : has logs

    TriggerExecutionProcessor --> TriggerExecution : processes
    TriggerExecutionProcessor --> StepProcessor : uses
    StepProcessor --> StepHandlerResolver : uses
    StepProcessor --> StepExecutionContext : creates
    StepProcessor --> StepResultBuilder : creates
    StepProcessor --> StepResult : receives

    StepProcessor --> Step : accesses via TriggerExecution

    StepHandlerResolver ..|> StepHandlerContract : returns implementation of
    StepHandlerContract --> StepExecutionContext : reads
    StepHandlerContract --> StepResultBuilder : writes
    StepResultBuilder ..|> LoggerInterface : implements
    StepResult --> StepExecutionLog : contains
```

## 🛠️ How to Use

### 1. Install dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```