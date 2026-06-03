<?php

declare(strict_types=1);

pest()->group('architecture');

arch()->preset()->php();
arch()->preset()->security();

arch()
    ->expect('App')
    ->toUseStrictTypes()
    ->not->toUse(['dd', 'ddd', 'dump', 'env', 'exit', 'ray', 'sleep', 'usleep']);
