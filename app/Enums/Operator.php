<?php

namespace App\Enums;

enum Operator: string
{
    case EQ = '==';
    case NEQ = '!=';
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case EXISTS = 'exists';
    case NOT_EXISTS = 'not_exists';
    case EMPTY = 'empty';
    case NOT_EMPTY = 'not_empty';
    case NULL = 'null';
    case NOT_NULL = 'not_null';
    case CONTAINS = 'contains';
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
    case MATCHES = 'matches';
    case IN = 'in';
    case NOT_IN = 'not_in';
}
