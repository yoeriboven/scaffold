<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem;

/** Daily: should be spread out */
Schedule::command('backup:clean')->dailyAt('07:00');
Schedule::command('backup:run')->dailyAt('07:05');
Schedule::command('model:prune', [
    '--model' => [MonitoredScheduledTaskLogItem::class],
])->dailyAt('07:10');

Schedule::command('cloudflare:reload')->dailyAt('09:00');

/** Others */
Schedule::command(DispatchQueueCheckJobsCommand::class)->everyMinute();

Schedule::command('horizon:snapshot')->everyFiveMinutes();

Schedule::command('calendar:send-showing-reminders')->everyMinute();
