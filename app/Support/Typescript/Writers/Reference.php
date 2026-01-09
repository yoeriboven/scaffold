<?php

declare(strict_types=1);

namespace App\Support\Typescript\Writers;

use Illuminate\Support\Str;
use Spatie\TypeScriptTransformer\Structures\TransformedType;

readonly class Reference
{
    public string $fqn;

    public string $path;

    public string $name;

    public function __construct(public TransformedType $type)
    {
        $this->fqn = $type->getTypeScriptName();
        $this->name = $type->getTypeScriptName(false);
        $this->path = Str::replace('.', '/', $type->getTypeScriptName());
    }
}
