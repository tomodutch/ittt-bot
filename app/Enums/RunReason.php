<?php
namespace App\Enums;

enum RunReason: string
{
    case Scheduled = "Scheduled";
    case Manual = "Manual";
    case Webhook = "Webhook";
}