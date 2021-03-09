<?php

namespace TanoConsulting\DataValidatorBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TanoConsulting\DataValidatorBundle\DependencyInjection\Compiler\MappingLoaderPass;

class TanoConsultingDataValidatorBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MappingLoaderPass());
    }
}
