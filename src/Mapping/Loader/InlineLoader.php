<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader;

use TanoConsulting\DataValidatorBundle\Mapping\Metadata;

/**
 * @todo should this be rather a factory?
 * @todo add a DynamicLoader class, which calls a Sf service and/or a php class method
 */
abstract class InlineLoader implements LoaderInterface
{
    protected  $constraintDefinitions;

    abstract protected function createConstraint($constraintDefinition);

    public function __construct(array $constraintDefinitions)
    {
        $this->constraintDefinitions = $constraintDefinitions;
    }

    public function loadMetadata(Metadata $metadata)
    {
        foreach($this->constraintDefinitions as $constraintDefinition)
        {
            $metadata->addConstraint($this->createConstraint($constraintDefinition));
        }
    }
}
