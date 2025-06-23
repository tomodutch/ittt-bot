<?php

namespace App\Services;

use Illuminate\Support\Collection;

interface ITriggerDataFetcher
{
    public function fetchData(Collection $triggers);
}