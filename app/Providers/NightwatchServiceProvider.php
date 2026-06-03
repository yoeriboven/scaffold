<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Nightwatch\Facades\Nightwatch;
use Laravel\Nightwatch\Records\Query;
use Laravel\Nightwatch\Records\QueuedJob;
use Spatie\ScheduleMonitor\Jobs\PingOhDearJob;

class NightwatchServiceProvider extends ServiceProvider
{
    private array $jobsToReject = [
        // vraag nightwatch waarom ik die healthqueuejob niet hoef te ignoren
//        PingOhDearJob::class,
    ];

    private array $commandsToReject = [
        'horizon:snapshot',
        'health:queue-check-heartbeat',
        // Should sample instead of reject. Use command as key and sample rate as value
        'calendar:send-showing-reminders',
    ];

    private array $tableQueriesToReject = [
        'monitored_scheduled_task_log_items',
        'monitored_scheduled_tasks',
    ];

    public function boot(): void
    {
        $this->rejectJobs();
        $this->rejectCommands();
        $this->rejectQueries();
    }

    private function rejectJobs(): void
    {
        // Als je dit doet staat er: 0 gequeued, x processed
        Nightwatch::rejectQueuedJobs(function (QueuedJob $job) {
            return in_array($job->name, $this->jobsToReject, true);
        });

        // Als je dit doet staat er: x gequeued, 0 geprocessed
        Event::listen(function (JobProcessing $event) {
            //            ray($event->job->resolveName());
            //            ray($event->job->resolveQueuedJobClass());
            if (in_array($event->job->resolveQueuedJobClass(), $this->jobsToReject, true)) {
                Nightwatch::dontSample();
            }
        });
    }

    private function rejectCommands(): void
    {
        Event::listen(function (CommandStarting $event) {
            if (in_array($event->command, $this->commandsToReject, true)) {
                Nightwatch::dontSample();
            }
        });

        // check if necessary?
        Event::listen(function (ScheduledTaskStarting $event) {
            foreach ($this->commandsToReject as $commandToReject) {
                if (str_contains($event->task->command, $commandToReject)) {
                    Nightwatch::dontSample();
                }
            }
        });
    }

    private function rejectQueries(): void
    {
        Nightwatch::rejectQueries(function (Query $query): bool {
            return array_any($this->tableQueriesToReject, static function ($table) use ($query): bool {
                return str_contains($query->sql, $table);
            });
        });
    }
}
