<?php

declare(strict_types=1);

use DI\ContainerBuilder;

require_once 'vendor/autoload.php';

return (new ContainerBuilder())
    ->addDefinitions(__DIR__ . '/services.php')
    ->build();
