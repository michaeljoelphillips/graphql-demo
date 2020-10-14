<?php

declare(strict_types=1);

use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Utils\BuildSchema;
use GraphQLDemo\Repository\CharacterRepository;
use GraphQLDemo\Resolver\AddCharacter;
use GraphQLDemo\Resolver\GetCharacters;
use GraphQLDemo\Resolver\ResolverMap;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

return [
    StandardServer::class => static function (ContainerInterface $container): StandardServer {
        $schemaDecorator = $container->get('schema.decorator');
        $schema          = BuildSchema::build(file_get_contents(__DIR__ . '/schema.demo.graphql'), $schemaDecorator);

        return new StandardServer(ServerConfig::create([
            'debugFlag' => 1,
            'schema' => $schema,
        ]));
    },
    CharacterRepository::class => static function (ContainerInterface $container): CharacterRepository {
        return new CharacterRepository();
    },
    'schema.decorator' => static function (ContainerInterface $container): callable {
        $queryResolver    = $container->get('query.resolver');
        $mutationResolver = $container->get('mutation.resolver');

        return static function ($config) use ($queryResolver, $mutationResolver): array {
            switch ($config['name']) {
                case 'Query':
                    $config['resolveField'] = $queryResolver;

                    break;
                case 'Mutation':
                    $config['resolveField'] = $mutationResolver;

                    break;
                default:
                    $config['resolveField'] = static function ($value, $args, $context, ResolveInfo $info) {
                        static $propertyAccess;

                        $propertyAccess = PropertyAccess::createPropertyAccessor();

                        return $propertyAccess->getValue($value, $info->fieldName);
                    };

                    break;
            }

            return $config;
        };
    },
    'query.resolver' => static function (ContainerInterface $container): callable {
        return new ResolverMap([
            'characters' => new GetCharacters($container->get(CharacterRepository::class)),
        ]);
    },
    'mutation.resolver' => static function (ContainerInterface $container): callable {
        return new ResolverMap([
            'addCharacter' => new AddCharacter($container->get(CharacterRepository::class)),
        ]);
    },
];
