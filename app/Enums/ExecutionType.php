<?php

namespace App\Enums;

enum ExecutionType: int
{
    case Schedule = 0;
    case Webhook = 1;
}