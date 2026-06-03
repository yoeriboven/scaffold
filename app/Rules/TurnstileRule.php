<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class TurnstileRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('services.turnstile.secret') === '') {
            return;
        }

        $response = Http::retry(3, 100)
            ->asForm()
            ->acceptJson()
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $value,
            ]);

        if ($response->json('success') === true) {
            return;
        }

        foreach ($response->json('error-codes') as $errorCode) {
            $fail(match ($errorCode) {
                'missing-input-secret' => __('validation.turnstile.missing-input-secret'),
                'invalid-input-secret' => __('validation.turnstile.invalid-input-secret'),
                'missing-input-response' => __('validation.turnstile.missing-input-response'),
                'invalid-input-response' => __('validation.turnstile.invalid-input-response'),
                'bad-request' => __('validation.turnstile.bad-request'),
                'timeout-or-duplicate' => __('validation.turnstile.timeout-or-duplicate'),
                'internal-error' => __('validation.turnstile.internal-error'),
                default => __('validation.turnstile.unexpected'),
            });
        }
    }
}
