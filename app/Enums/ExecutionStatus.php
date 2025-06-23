<?php
namespace App\Enums;

enum ExecutionStatus : int {
    case Idle = 0;
    case Running = 1;
    case Finished = 2;
}
