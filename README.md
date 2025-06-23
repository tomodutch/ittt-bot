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
        int executionType        // 0 = schedule, 1 = webhook
        string timezone
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    class Schedule {
        UUID id
        UUID triggerId
        int typeCode
        datetime? oneTimeAt       // for 'once'
        time? runTime             // for 'daily' and 'weekly'
        smallint[]? daysOfWeek    // for 'weekly' (1 = Mon, 7 = Sun)
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    class TriggerExecution {
        UUID id
        UUID triggerId
        string? originType   // 'user', 'system', 'api', 'webhook'
        string? originId     // UUID or identifier
        int statusCode
        int runReasonCode // 0 = scheduled, 1 = manual, 2 = webhook
        jsonb context
        datetime startedAt
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
        int typeCode // 0 = conditional, 1 = action
        jsonb? expressionTree
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
        int statusCode
        jsonb input
        jsonb output
        string? errorMessage
        datetime startedAt
        datetime finishedAt
        datetime createdAt
        datetime updatedAt
        datetime deletedAt
    }

    Trigger "1" --> "many" Schedule : has
    Trigger "1" --> "many" Step : has
    Trigger "1" --> "many" TriggerExecution : runs
    TriggerExecution "1" --> "many" StepExecutionLog : logs
    Step "1" --> "many" StepExecutionLog : has logs
```

## 🛠️ How to Use

### 1. Install dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```