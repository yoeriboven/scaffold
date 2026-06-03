<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Eloquent\MissingAttributeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureEloquent();
        $this->configureMorphMap();
        $this->configureAssetPrefetching();
        $this->configureDates();
        $this->configureCommands();
        $this->configureURL();
        $this->configureEmailRecipientOnLocal();
        $this->configureRateLimiting();
        $this->configurePasswordRules();
    }

    private function configureEloquent(): void
    {
        Model::unguard();

        Model::shouldBeStrict();

        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            $exception = new LazyLoadingViolationException($model, $relation);

            $this->app->isProduction() ? report($exception) : throw $exception;
        });

        Model::handleMissingAttributeViolationUsing(function ($model, $attribute) {
            $exception = new MissingAttributeException($model, $attribute);

            $this->app->isProduction() ? report($exception) : throw $exception;
        });
    }

    private function configureMorphMap(): void
    {
        Relation::enforceMorphMap([
        ]);
    }

    private function configureAssetPrefetching(): void
    {
        Vite::prefetch(concurrency: 3);
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    private function configureURL(): void
    {
        URL::forceScheme('https');
    }

    private function configureEmailRecipientOnLocal(): void
    {
        if (app()->isLocal()) {
            Mail::alwaysTo('yoeri@yoeri.me');
        }
    }

    private function configureRateLimiting(): void {}

    private function configurePasswordRules(): void
    {
        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
