<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;

$expectedCommands = [
    ['name' => 'backup:clean', 'expression' => '0 7 * * *'],
    ['name' => 'backup:run', 'expression' => '5 7 * * *'],
    ['name' => 'model:prune --model=\'Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem\'', 'expression' => '10 7 * * *'],
    ['name' => 'health:queue-check-heartbeat', 'expression' => '* * * * *'],
    ['name' => 'horizon:snapshot', 'expression' => '*/5 * * * *'],
    ['name' => 'cloudflare:reload', 'expression' => '0 9 * * *'],
];

it('has the correct amount of commands scheduled', function () use ($expectedCommands) {
    $commands = app(Schedule::class)->events();

    expect($commands)->toHaveCount(
        count($expectedCommands),
        count($commands) > count($expectedCommands)
            ? 'More commands scheduled than expected.'
            : 'Not all expected commands are scheduled.'
    );
});

it('has these commands scheduled', function (string $name, string $expression) {
    $commands = collect(app(Schedule::class)->events());

    $command = $commands->first(static fn (Event $event): bool => Str::contains($event->command, $name));

    $this->assertNotNull($command?->command, "$name should have been scheduled.".PHP_EOL.PHP_EOL.$commands->pluck('command')->implode(PHP_EOL).PHP_EOL);
    expect($command->expression)->toBe($expression, "Incorrect expression for command: $name");
})->with($expectedCommands);
