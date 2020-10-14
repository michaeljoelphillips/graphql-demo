<?php

declare(strict_types=1);

namespace GraphQLDemo\Resolver;

use GraphQLDemo\Repository\CharacterRepository;

class GetCharacters
{
    private CharacterRepository $repository;

    public function __construct(CharacterRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($arguments)
    {
        return $this->repository->getCharacters();
    }
}
