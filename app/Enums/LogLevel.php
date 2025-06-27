<?php

namespace App\Enums;

enum LogLevel: string
{
    case Emergency = 'Emergency';
    case Alert = 'Alert';
    case Critical = 'Critical';
    case Error = 'Error';
    case Warning = 'Warning';
    case Notice = 'Notice';
    case Info = 'Info';
    case Debug = 'Debug';
}
