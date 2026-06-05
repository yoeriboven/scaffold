<?php

declare(strict_types=1);

use App\Enums\Queue;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

describe('scheduling', function () {
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
});

describe('horizon supervisors', function () {
    it('leaves the horizon defaults empty', function () {
        expect(config('horizon.defaults'))->toBe([]);
    });

    it('registers a single wildcard environment', function () {
        expect(config('horizon.environments'))
            ->toHaveCount(1)
            ->toHaveKey('*');
    });

    it('creates a supervisor for every queue', function (Queue $queue) {
        expect(config('horizon.environments.*'))
            ->toHaveKey("supervisor-{$queue->value}");
    })->with(Queue::cases());

    it('creates no supervisors beyond the queue enum cases', function () {
        $expected = collect(Queue::cases())
            ->map(fn (Queue $queue): string => "supervisor-{$queue->value}")
            ->all();

        expect(array_keys(config('horizon.environments.*')))
            ->toEqualCanonicalizing($expected);
    });

    it('orders each supervisor queue with its own queue first, then the rest alphabetically', function (Queue $queue) {
        $rest = collect(Queue::cases())
            ->map(fn (Queue $other): string => $other->value)
            ->reject(fn (string $value): bool => $value === $queue->value)
            ->sort()
            ->values()
            ->all();

        expect(config("horizon.environments.*.supervisor-{$queue->value}.queue"))
            ->toBe([$queue->value, ...$rest]);
    })->with(Queue::cases());

    it('uses the custom defaults unchanged for a queue without overrides', function () {
        /** @var Queue $queueWithoutOverrides */
        $queueWithoutOverrides = Arr::first(
            Queue::cases(),
            fn (Queue $queue): bool => ! array_key_exists($queue->value, config('horizon.custom.queues')),
        );

        $supervisorOfQueueWithoutOverrides = config("horizon.environments.*.supervisor-{$queueWithoutOverrides->value}");
        $defaultSupervisor = config('horizon.custom.defaults');

        expect($supervisorOfQueueWithoutOverrides)
            // Queue is always custom so we dont need to check that
            ->toMatchArray(Arr::except($defaultSupervisor, 'queue'));
    });

    it('overrides default options per queue', function () {
        $defaults = config('horizon.custom.defaults');
        $overrides = config('horizon.custom.queues', []);

        /** @var Queue $customizedQueue */
        $customizedQueue = Arr::first(
            Queue::cases(),
            fn (Queue $queue): bool => array_key_exists($queue->value, config('horizon.custom.queues')),
        );

        $queueOverrides = $overrides[$customizedQueue->value];
        $supervisor = config("horizon.environments.*.supervisor-{$customizedQueue->value}");

        // Overridden keys win.
        foreach ($queueOverrides as $key => $value) {
            expect($supervisor[$key])->toBe($value);
        }

        // Every other default option still flows through untouched.
        foreach (array_diff_key($defaults, $queueOverrides, ['queue' => null]) as $key => $value) {
            expect($supervisor[$key])->toBe($value);
        }
    });
});
