<?php

declare(strict_types=1);

use App\Enums\Queue;
use App\Support\Horizon\HorizonConfig;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
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

describe('horizon supervisor configuration', function () {
    beforeEach(function () {
        config()->set('horizon.custom', [
            // `queue` here must always be overwritten by the ordered list.
            'defaults' => ['timeout' => 90, 'tries' => 5, 'queue' => ['ignored']],
            'queues' => [
                Queue::Default->value => ['tries' => 1],
            ],
        ]);
    });

    it('leaves the defaults empty', function () {
        expect((new HorizonConfig)->toArray()['defaults'])->toBe([]);
    });

    it('turns the custom config into a single wildcard environment of supervisors', function () {
        $config = new HorizonConfig;

        // One supervisor per queue: overrides win over defaults, and the queue
        // lists itself first then the others alphabetically.
        $expected = [];

        foreach (Queue::cases() as $queue) {
            // The default settings in beforeEach. Note `queue` is the ordered
            // list, never the `['ignored']` we put in the custom defaults.
            $worker = [
                'timeout' => 90,
                'tries' => 5,
                'queue' => $config->orderedQueues()[$queue->value],
            ];

            // Default queue overrides `tries`.
            if ($queue === Queue::Default) {
                $worker['tries'] = 1;
            }

            $expected["supervisor-{$queue->value}"] = $worker;
        }

        expect($config->toArray()['environments'])->toEqual(['*' => $expected]);
    });

    it('orders each supervisor queue with its own queue first, then the rest alphabetically', function (Queue $queue) {
        $rest = collect(Queue::cases())
            ->map(fn (Queue $other): string => $other->value)
            ->reject(fn (string $value): bool => $value === $queue->value)
            ->sort()
            ->values()
            ->all();

        $environments = (new HorizonConfig)->toArray()['environments'];

        expect($environments['*']["supervisor-{$queue->value}"]['queue'])
            ->toBe([$queue->value, ...$rest]);
    })->with(Queue::cases());
});

describe('horizon service provider', function () {
    it('merges the compiled supervisor config onto the native horizon config', function () {
        $horizonConfig = (new HorizonConfig)->toArray();

        // HorizonServiceProvider sets config('horizon') to our values at booth
        expect(config('horizon.defaults'))->toBe($horizonConfig['defaults']);
        expect(config('horizon.environments'))->toEqual($horizonConfig['environments']);

        // ...while leaving every native Horizon key in place (merged, not replaced).
        expect(config('horizon'))->toHaveKeys([
            'name',
            'domain',
            'path',
            'use',
            'prefix',
            'middleware',
            'waits',
            'trim',
            'silenced',
            'silenced_tags',
            'metrics',
            'fast_termination',
            'memory_limit',
            'watch',
            'custom',
        ]);
    });
});
