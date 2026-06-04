<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HealthServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\MacroServiceProvider;
use App\Providers\NightwatchServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    HealthServiceProvider::class,
    HorizonServiceProvider::class,
    NightwatchServiceProvider::class,
    MacroServiceProvider::class,
];
