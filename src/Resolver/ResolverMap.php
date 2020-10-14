<?php

declare(strict_types=1);

namespace GraphQLDemo\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use OutOfBoundsException;

use function array_key_exists;
use function sprintf;

class ResolverMap
{
    /** @var array<string, callable> */
    private array $map;

    /**
     * @param array<string, callable> $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param mixed $value
     * @param mixed $args
     * @param mixed $context
     *
     * @return mixed
     */
    public function __invoke($value, $args, $context, ResolveInfo $info)
    {
        if (array_key_exists($info->fieldName, $this->map)) {
            return $this->map[$info->fieldName]($args);
        }

        throw new OutOfBoundsException(sprintf('No resolver exists for field name %s', $info->fieldName));
    }
}
