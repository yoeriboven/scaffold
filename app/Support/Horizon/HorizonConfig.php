<?php

declare(strict_types=1);

namespace App\Support\Horizon;

use App\Enums\Queue;

class HorizonConfig
{
    /**
     * The native `horizon` config with our supervisors overlaid on top.
     *
     * Every queue in the {@see Queue} enum becomes a `supervisor-{queue}` key
     * whose worker config is `custom.defaults` with any matching
     * `custom.queues.{queue}` overrides merged on top. The worker's `queue` is
     * its own queue first, then the rest alphabetically. All supervisors live
     * under a single `*` environment, `defaults` is left empty, and every other
     * native Horizon key is preserved.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_replace(config('horizon'), [
            'defaults' => [],
            'environments' => ['*' => $this->supervisors()],
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function supervisors(): array
    {
        $defaults = config('horizon.custom.defaults', []);
        $overrides = config('horizon.custom.queues', []);
        $orderedQueues = $this->orderedQueues();

        return collect(Queue::cases())
            ->mapWithKeys(function (Queue $queue) use ($defaults, $overrides, $orderedQueues): array {
                $worker = array_replace($defaults, $overrides[$queue->value] ?? []);
                $worker['queue'] = $orderedQueues[$queue->value];

                return ["supervisor-{$queue->value}" => $worker];
            })
            ->all();
    }

    /**
     * Build the priority list for every queue: its own queue first, then the
     * remaining queues in alphabetical order. This way each supervisor
     * prioritizes its own queue but still drains the others when idle.
     *
     * [
     *     'default'    => ['default', 'monitoring'],
     *     'monitoring' => ['monitoring', 'default'],
     * ]
     *
     * @return array<string, list<string>>
     */
    public function orderedQueues(): array
    {
        $all = Queue::values();

        $lists = [];

        foreach ($all as $primary) {
            $rest = array_values(array_filter($all, fn (string $queue): bool => $queue !== $primary));
            sort($rest);

            $lists[$primary] = [$primary, ...$rest];
        }

        return $lists;
    }
}
