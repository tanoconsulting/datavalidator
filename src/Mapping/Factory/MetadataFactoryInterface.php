<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Factory;

use TanoConsulting\DataValidatorBundle\Mapping\MetadataInterface;

interface MetadataFactoryInterface
{
    /**
     * Returns the metadata.
     *
     * @return MetadataInterface The metadata
     */
    public function getMetadata();
}
