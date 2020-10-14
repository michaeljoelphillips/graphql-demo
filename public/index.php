<?php

declare(strict_types=1);

use GraphQL\Server\StandardServer;

$container = require_once __DIR__ . '/../bootstrap.php';
$server    = $container->get(StandardServer::class);

try {
    $server->handleRequest();
} catch (Throwable $t) {
}
