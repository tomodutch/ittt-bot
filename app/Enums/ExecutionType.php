<?php

namespace App\Enums;

enum ExecutionType: string
{
    case Schedule = "Schedule";
    case Webhook = "Webhook";
}