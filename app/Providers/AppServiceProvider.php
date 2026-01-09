<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\MissingAttributeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        $this->configureAuthentication();
        $this->configureFactories();
        $this->configureEmailRecipientOnLocal();
        $this->configureRateLimiting();
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
        Relation::enforceMorphMap([]);
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

    private function configureAuthentication(): void
    {
        //
    }

    // Check Factory::resolveFactoryName() to see why we need this.
    // I think because we use Domains namespace instead of App
    private function configureFactories(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return sprintf('Database\\Factories\\%sFactory', Str::afterLast($modelName, '\\'));
        });
    }

    private function configureEmailRecipientOnLocal(): void
    {
        if (app()->isLocal()) {
            Mail::alwaysTo('yoeri@yoeri.me');
        }
    }

    private function configureRateLimiting(): void
    {
        //
    }
}
