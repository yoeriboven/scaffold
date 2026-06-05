<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Queue;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureSupervisors();

        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        Horizon::routeMailNotificationsTo('yoeri@yoeri.me');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Translate our `horizon.custom` config into Horizon's `environments`.
     *
     * Every queue in the {@see Queue} enum becomes a `supervisor-{queue}` key
     * whose worker config is `custom.defaults` with any matching
     * `custom.queues.{queue}` overrides merged on top. The worker's `queue` is
     * the queue's own name in an array. All supervisors live under a single
     * `*` environment so every environment runs the same set, and
     * `horizon.defaults` is left empty.
     */
    private function configureSupervisors(): void
    {
        $defaults = config('horizon.custom.defaults', []);
        $overrides = config('horizon.custom.queues', []);

        $supervisors = collect(Queue::cases())
            ->mapWithKeys(function (Queue $queue) use ($defaults, $overrides): array {
                $worker = array_replace($defaults, $overrides[$queue->value] ?? []);
                $worker['queue'] = [$queue->value];

                return ["supervisor-{$queue->value}" => $worker];
            })
            ->all();

        config([
            'horizon.defaults' => [],
            'horizon.environments' => ['*' => $supervisors],
        ]);
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (User $user): bool {
            return $user->isPlatformAdmin();
        });
    }
}
