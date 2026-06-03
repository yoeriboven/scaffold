<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Request::macro('usesController', function (string|array $controllers) {
            $controllers = Arr::wrap($controllers);

            return $this->route() && in_array($this->route()->getControllerClass(), $controllers);
        });
    }
}
