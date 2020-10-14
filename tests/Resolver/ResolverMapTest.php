<?php

declare(strict_types=1);

namespace GraphQLDemo\Tests\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQLDemo\Resolver\ResolverMap;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class ResolverMapTest extends TestCase
{
    public function testResolverThrowsExeptionWhenClosureIsNotDefined(): void
    {
        $subject = new ResolverMap([]);

        $info            = $this->createMock(ResolveInfo::class);
        $info->fieldName = 'testField';

        $this->expectException(OutOfBoundsException::class);

        $subject([], [], [], $info);
    }

    public function testResolverDelegatesToAMappedClosure(): void
    {
        $subject = new ResolverMap([
            'testField' => fn () => $this->addToAssertionCount(1),
        ]);

        $info            = $this->createMock(ResolveInfo::class);
        $info->fieldName = 'testField';

        $subject([], [], [], $info);
    }
}
