<?php

declare(strict_types=1);

namespace App\Support\Typescript\Transformers;

use Override;
use ReflectionClass;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Transformers\Transformer;

class EnumTransformer implements Transformer
{
    #[Override]
    public function transform(ReflectionClass $class, string $name): ?TransformedType
    {
        if (! $class->isEnum()) {
            return null;
        }

        $enum = (new ReflectionEnum($class->getName()));

        if (! $enum->isBacked()) {
            return null;
        }

        $transformed = collect($enum->getCases())
            ->map(static fn (ReflectionEnumBackedCase $case): string => sprintf("%s: '%s',", $case->getName(), $case->getBackingValue()))
            ->join(PHP_EOL.'    ');

        return TransformedType::create(
            $class,
            $name,
            $transformed,
            keyword: 'const',
        );
    }
}
