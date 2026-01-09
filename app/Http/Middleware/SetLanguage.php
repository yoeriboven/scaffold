<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Language;
use Closure;
use Illuminate\Http\Request;

class SetLanguage
{
    public function handle(Request $request, Closure $next)
    {
        $language = $request->user()
            ? $request->user()->language->value
            : $this->acceptLanguageHeader($request);

        app()->setLocale($language);

        return $next($request);
    }

    private function acceptLanguageHeader(Request $request): string
    {
        $list = explode(',', $request->server('HTTP_ACCEPT_LANGUAGE', ''));

        $locales = collect($list)
            ->map(function ($locale) {
                $parts = explode(';', $locale);

                $mapping['locale'] = str_replace('-', '_', trim($parts[0]));

                if (isset($parts[1])) {
                    $factorParts = explode('=', $parts[1]);

                    $mapping['factor'] = $factorParts[1];
                } else {
                    $mapping['factor'] = 1;
                }

                return $mapping;
            })
            ->filter(function ($mapping) {
                return str_contains($mapping['locale'], '_');
            })
            ->filter(function ($mapping) {
                return Language::tryFrom($mapping['locale']);
            })
            ->sortByDesc(function ($locale) {
                return $locale['factor'];
            });

        return $locales->first()['locale'];
    }
}
