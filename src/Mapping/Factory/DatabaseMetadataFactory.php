<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Factory;

use TanoConsulting\DataValidatorBundle\Mapping\DatabaseMetaData;

class DatabaseMetadataFactory extends MetadataFactory implements MetadataFactoryInterface
{
    public function getMetadata()
    {
        $metadata = new DatabaseMetaData();
        $this->loader->loadMetadata($metadata);
        return $metadata;
    }
}
