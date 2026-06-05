<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Support\Horizon\HorizonConfig;
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
        $this->applyHorizonConfig();

        $this->authorization();
    }

    /**
     * Swap in our Horizon config that setups up supervisors.
     */
    private function applyHorizonConfig(): void
    {
        config(['horizon' => new HorizonConfig()->toArray()]);
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
