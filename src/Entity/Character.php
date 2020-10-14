<?php

declare(strict_types=1);

namespace GraphQLDemo\Entity;

class Character
{
    public string $name;

    /** @var array<int, string> */
    public array $quotes = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
