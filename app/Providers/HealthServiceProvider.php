<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enum\Queue;
use Illuminate\Support\ServiceProvider;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\BackupsCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;

class HealthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Health::checks([
            //            BackupsCheck::new()
            //                ->name('Backup status (stored on server)')
            //                ->locatedAt(storage_path('app/private/backups/*.zip'))
            //                ->numberOfBackups(min: 5, max: 100)
            //                ->youngestBackShouldHaveBeenMadeBefore(now()->subDay())
            //                ->oldestBackShouldHaveBeenMadeAfter(now()->subMonth())
            //                ->daily(),
            //
            //            BackupsCheck::new()
            //                ->name('Backup status (stored on R2)')
            //                ->onDisk('r2-private')
            //                ->locatedAt('backups')
            //                ->numberOfBackups(min: 5, max: 100)
            //                ->youngestBackShouldHaveBeenMadeBefore(now()->subDay())
            //                ->oldestBackShouldHaveBeenMadeAfter(now()->subMonth())
            //                ->daily(),
            //
            //            BackupsCheck::new()
            //                ->name('Backup status (stored on S3)')
            //                ->onDisk('s3-private')
            //                ->locatedAt('backups')
            //                ->numberOfBackups(min: 5, max: 100)
            //                ->youngestBackShouldHaveBeenMadeBefore(now()->subDay())
            //                ->oldestBackShouldHaveBeenMadeAfter(now()->subMonth())
            //                ->daily(),

            CacheCheck::new(),

            OptimizedAppCheck::new()
                ->checkConfig()
                ->checkRoutes(),

            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(1.5),

            DatabaseCheck::new(),

            DebugModeCheck::new(),

            EnvironmentCheck::new()
                ->expectEnvironment('production'),

            HorizonCheck::new(),

            //            QueueCheck::new()
            //                ->onQueue(Queue::values()),

            RedisCheck::new(),

            RedisMemoryUsageCheck::new()
                ->warnWhenAboveMb(500)
                ->failWhenAboveMb(7000),

            SecurityAdvisoriesCheck::new(),

            UsedDiskSpaceCheck::new(),

        ]);
    }
}
