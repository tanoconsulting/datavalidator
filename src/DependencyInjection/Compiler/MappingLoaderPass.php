<?php

namespace TanoConsulting\DataValidatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Database\TaggedServiceLoader as DatabaseServiceLoader;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Filesystem\TaggedServiceLoader as FilesystemServiceLoader;

class MappingLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has(DatabaseServiceLoader::class)) {
            $definition = $container->findDefinition(DatabaseServiceLoader::class);

            $taggedServices = $container->findTaggedServiceIds('data_validator.constraint_provider.database');

            foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall('addConstraintDefinitionProvider', [new Reference($id)]);
            }
        }

        if ($container->has(FilesystemServiceLoader::class)) {
            $definition = $container->findDefinition(FilesystemServiceLoader::class);

            $taggedServices = $container->findTaggedServiceIds('data_validator.constraint_provider.filesystem');

            foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall('addConstraintDefinitionProvider', [new Reference($id)]);
            }
        }
    }
}
