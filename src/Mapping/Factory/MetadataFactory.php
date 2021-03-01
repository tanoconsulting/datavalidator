<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Factory;

use TanoConsulting\DataValidatorBundle\Mapping\Loader\LoaderInterface;

abstract class MetadataFactory
{
    protected $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }
}
