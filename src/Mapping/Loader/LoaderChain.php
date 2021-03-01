<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader;

use TanoConsulting\DataValidatorBundle\Exception\MappingException;
use TanoConsulting\DataValidatorBundle\Mapping\Metadata;

class LoaderChain implements LoaderInterface
{
    protected $loaders;

    /**
     * @param LoaderInterface[] $loaders The metadata loaders to use
     *
     * @throws MappingException If any of the loaders has an invalid type
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!$loader instanceof LoaderInterface) {
                throw new MappingException(sprintf('Class "%s" is expected to implement LoaderInterface.', get_debug_type($loader)));
            }
        }

        $this->loaders = $loaders;
    }

    public function loadMetadata(Metadata $metadata)
    {
        $success = false;

        foreach ($this->loaders as $loader) {
            $success = $loader->loadMetadata($metadata) || $success;
        }

        return $success;
    }
}
