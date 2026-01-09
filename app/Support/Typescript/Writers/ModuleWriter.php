<?php

declare(strict_types=1);

namespace App\Support\Typescript\Writers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Override;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Structures\TypesCollection;
use Spatie\TypeScriptTransformer\Writers\Writer;

class ModuleWriter implements Writer
{
    private const string ENUMS_PATH = 'js/enums/generated';

    private const string TYPES_PATH = 'js/types/generated';

    #[Override]
    public function format(TypesCollection $collection): string
    {
        $filesystem = (new Filesystem);

        $filesystem->deleteDirectory(resource_path(self::ENUMS_PATH));
        $filesystem->deleteDirectory(resource_path(self::TYPES_PATH));

        $references = $this->buildReferenceList($collection);

        /** @var \Spatie\TypeScriptTransformer\Structures\TransformedType $type */
        foreach ($collection as $type) {
            if ($type->isInline) {
                continue;
            }

            $pathWithoutExtension = $this->getPathWithoutExtension($type);

            if ($type->keyword === 'const') {
                $contents = $this->generateEnumScript($type);
                $path = $pathWithoutExtension.'.ts';
            } else {
                $contents = $this->generateTypeDefinition($type, $references);
                $path = $pathWithoutExtension.'.d.ts';
            }

            $filesystem->makeDirectory(dirname($path), 0755, true, true);
            $filesystem->put($path, $contents);
        }

        // We don't use the generated.d.ts file, so it can remain empty
        return '';
    }

    #[Override]
    public function replacesSymbolsWithFullyQualifiedIdentifiers(): bool
    {
        return true;
    }

    private function buildReferenceList(TypesCollection $collection): Collection
    {
        return collect($collection)->mapWithKeys(static function (TransformedType $type, string $index): array {
            return [$type->getTypeScriptName() => new Reference($type)];
        });
    }

    private function generateEnumScript(TransformedType $type): string
    {
        return <<<TS
            export const {$type->name} = {
                {$type->transformed}
            } as const

            TS;
    }

    private function generateTypeDefinition(TransformedType $type, Collection $references): string
    {
        $transformed = mb_trim($type->transformed, '{}'.PHP_EOL);
        $transformed = preg_replace('/^/m', '    ', $transformed);

        $contents = <<<TS
            export type {$type->name} = {
            {$transformed}
            }

            TS;

        return $this->addImports($references, $contents);
    }

    private function addImports(Collection $references, string $contents): string
    {
        $imports = [];

        /** @var \App\Typescript\Writers\Reference $reference */
        foreach ($references as $reference) {
            if (Str::contains($contents, $reference->fqn)) {
                $typeName = $reference->type->keyword === 'const'
                    ? sprintf('typeof %s[keyof typeof %s]', $reference->name, $reference->name)
                    : $reference->name;

                // Convert FQN classes to simple class names
                $contents = Str::replace($reference->fqn, $typeName, $contents);

                $importPath = $this->getPathWithoutExtension($reference->type);

                $importPath = Str::replace(resource_path(), '@', $importPath);
                $importPath = Str::replace(DIRECTORY_SEPARATOR, '/', $importPath);

                $imports[] = sprintf("import type { %s } from '%s'", $reference->name, $importPath);
            }
        }

        if (count($imports) > 0) {
            sort($imports);

            return implode(PHP_EOL, $imports).PHP_EOL.PHP_EOL.$contents;
        }

        return $contents;
    }

    private function getPathWithoutExtension(TransformedType $type): string
    {
        $basePath = match ($type->keyword) {
            'const' => resource_path(self::ENUMS_PATH),
            'type' => resource_path(self::TYPES_PATH),
        };

        return $basePath.DIRECTORY_SEPARATOR.Str::replace('.', DIRECTORY_SEPARATOR, $type->getTypeScriptName());
    }
}
