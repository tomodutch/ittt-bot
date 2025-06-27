<?php

namespace App\Console\Commands;

use App\Jobs\RunTriggerExecution;
use App\Services\DueScheduleFinder;
use App\Services\TriggerOrchestrator;
use Illuminate\Console\Command;

class ScheduleTriggers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:triggers:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule triggers for execution';

    public function __construct(private DueScheduleFinder $dueScheduleFinder, private TriggerOrchestrator $triggerOrchestrator)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding due schedules...');
        $dueSchedules = $this->dueScheduleFinder->find();

        if ($dueSchedules->isEmpty()) {
            $this->info('No due schedules found.');
            return;
        }

        $this->info("Found {$dueSchedules->count()} due schedules.");
        
        // Process the due schedules
        $this->info('Scheduling triggers...');
        $this->triggerOrchestrator->process($dueSchedules);
        $this->info('Triggers scheduled successfully.');
    }
}
