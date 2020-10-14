<?php

declare(strict_types=1);

namespace GraphQLDemo\Resolver;

use GraphQLDemo\Entity\Character;
use GraphQLDemo\Repository\CharacterRepository;

class AddCharacter
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
        $input     = $arguments['input'];
        $character = new Character($input['name']);

        $this->repository->addCharacter($character);

        return $character;
    }
}
