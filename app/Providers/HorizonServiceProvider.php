<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Horizon\HorizonConfig;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class HorizonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureHorizon();
        $this->configureAuthorization();
    }

    private function configureHorizon(): void
    {
        config(['horizon' => new HorizonConfig()->toArray()]);
    }

    protected function configureAuthorization(): void
    {
        Horizon::auth(function ($request) {
            return app()->isLocal() || $request->user()->isPlatformAdmin();
        });
    }
}
