<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Factory;

use TanoConsulting\DataValidatorBundle\Mapping\DatabaseMetaData;

class DatabaseMetadataFactory extends MetadataFactory implements MetadataFactoryInterface
{
    protected $metadata;

    public function getMetadata()
    {
        if (!$this->metadata) {
            $this->metadata = new DatabaseMetaData();
            $this->loader->loadMetadata($this->metadata);
        }
        return $this->metadata;
    }
}
