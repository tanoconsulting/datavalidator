<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader;

use TanoConsulting\DataValidatorBundle\Exception\MappingException;
use TanoConsulting\DataValidatorBundle\Mapping\Metadata;

/**
 * An 'in memory store' for constraints definitions
 */
abstract class LoaderBag extends AbstractLoader implements LoaderInterface
{
    protected $constraintDefinitions = [];

    public function __construct(array $constraintDefinitions = [])
    {
        $this->constraintDefinitions = $constraintDefinitions;
    }

    public function addConstraintDefinition($constraintDefinition)
    {
        $this->constraintDefinitions[] = $constraintDefinition;
    }

    public function addConstraintDefinitions(array $constraintDefinitions)
    {
        $this->constraintDefinitions = array_merge($this->constraintDefinitions, $constraintDefinitions);
    }

    /**
     * @param Metadata $metadata
     * @throws MappingException
     */
    public function loadMetadata(Metadata $metadata)
    {
        foreach($this->constraintDefinitions as $constraintDefinition)
        {
            if (!is_array($constraintDefinition) || count($constraintDefinition) !== 1) {
                throw new MappingException('Invalid config file syntax');
            }
            $class = key($constraintDefinition);
            $options = current($constraintDefinition);
            $metadata->addConstraint($this->newConstraint($class, $options));
        }
    }
}
