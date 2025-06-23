<?php

namespace App\Enums;

enum ScheduleType: int
{
    case Once = 0;
    case Daily = 1;
    case Weekly = 2;
}