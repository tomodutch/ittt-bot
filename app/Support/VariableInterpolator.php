<?php

namespace App\Support;

use Illuminate\Support\Collection;

class VariableInterpolator
{
    public static function interpolate(string $input, array|Collection $context): string
    {
        return preg_replace_callback('/{{\s*([\w\.]+)\s*}}/', function ($matches) use ($context) {
            return (string) data_get($context, $matches[1], '');
        }, $input);
    }
}
