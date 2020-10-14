<?php

declare(strict_types=1);

namespace GraphQLDemo\Repository;

use GraphQLDemo\Entity\Character;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function serialize;
use function unserialize;

class CharacterRepository
{
    /** @var array<int, Character> */
    private array $characters;

    public function __construct()
    {
        $this->characters = $this->initialize();
    }

    public function __destruct()
    {
        file_put_contents('/tmp/characters.php', serialize($this->characters));
    }

    /**
     * @return array<int, Character>
     */
    private function initialize(): array
    {
        if (file_exists('/tmp/characters.php')) {
            return unserialize(file_get_contents('/tmp/characters.php'));
        }

        return [];
    }

    /**
     * @return array<int, Character>
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): void
    {
        $this->characters[] = $character;
    }
}
