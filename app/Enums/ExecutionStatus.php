<?php

namespace App\Enums;

enum ExecutionStatus: string
{
    case Idle = 'Idle';
    case Running = 'Running';
    case Finished = 'Finished';
}
