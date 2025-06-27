<?php

namespace App\Enums;

enum ScheduleType: string
{
    case Once = 'Once';
    case Daily = 'Daily';
    case Weekly = 'Weekly';
}
