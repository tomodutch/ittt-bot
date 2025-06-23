<?php
namespace App\Enums;

enum RunReason: int
{
    case Scheduled = 0;
    case Manual = 1;
    case Webhook = 2;
}