<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
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
use Spatie\Health\Enums\Status;
use Spatie\Health\Events\CheckEndedEvent;
use Spatie\Health\Facades\Health;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;

class HealthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureChecks();
        $this->autoPatchSecurityAdvisories();
    }

    private function configureChecks(): void
    {
        Health::checks([
            BackupsCheck::new()
                ->name('Backup status (stored on server)')
                ->locatedAt(storage_path('app/private/backups/*.zip'))
                ->numberOfBackups(min: 5, max: 100)
                ->youngestBackShouldHaveBeenMadeBefore(now()->subDay())
                // Gradually increase this number until it is the desired value
                ->oldestBackShouldHaveBeenMadeAfter(now()->subMonth())
                ->daily(),

            BackupsCheck::new()
                ->name('Backup status (stored on R2)')
                ->onDisk('r2-private')
                ->locatedAt('backups')
                ->numberOfBackups(min: 5, max: 100)
                ->youngestBackShouldHaveBeenMadeBefore(now()->subDay())
                // Gradually increase this number until it is the desired value
                ->oldestBackShouldHaveBeenMadeAfter(now()->subMonth())
                ->daily(),

            BackupsCheck::new()
                ->name('Backup status (stored on S3)')
                ->onDisk('s3-private')
                ->locatedAt('backups')
                ->numberOfBackups(min: 5, max: 100)
                ->youngestBackShouldHaveBeenMadeBefore(now()->subDay())
                // Gradually increase this number until it is the desired value
                ->oldestBackShouldHaveBeenMadeAfter(now()->subMonth())
                ->daily(),

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

            QueueCheck::new()
                ->onQueue(Queue::values()),

            RedisCheck::new(),

            RedisMemoryUsageCheck::new()
                ->warnWhenAboveMb(500)
                ->failWhenAboveMb(7000),

            SecurityAdvisoriesCheck::new(),

            UsedDiskSpaceCheck::new(),
        ]);
    }

    private function autoPatchSecurityAdvisories(): void
    {
        Event::listen(CheckEndedEvent::class, function (CheckEndedEvent $event) {
            if (! $event->check instanceof SecurityAdvisoriesCheck) {
                return;
            }

            if ($event->result->status === Status::ok()) {
                return;
            }

            $cacheKey = collect($event->result->meta)
                ->flatten(1)
                ->pluck('advisoryId')
                ->prepend('composer-security-advisories-failed:')
                ->join('&');

            if (Cache::has($cacheKey)) {
                return;
            }

            $affectedPackages = array_keys($event->result->meta);

            $response = Http::createPendingRequest()
                ->withToken(config('services.github.workflow_trigger_token'))
                ->post('https://api.github.com/repos/yoeriboven/[repo]/dispatches', [
                    'event_type' => 'composer-security-advisories-failed',
                    'client_payload' => [
                        'packages' => $affectedPackages,
                    ],
                ]);

            if ($response->successful()) {
                Cache::put($cacheKey, true, now()->addWeek());
            } else {
                report($response->toException());
            }
        });
    }
}
