<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Factory;

use TanoConsulting\DataValidatorBundle\Mapping\FilesystemMetaData;

class FilesystemMetadataFactory extends MetadataFactory implements MetadataFactoryInterface
{
    protected $metadata;

    public function getMetadata()
    {
        if (!$this->metadata) {
            $this->metadata = new FilesystemMetaData();
            $this->loader->loadMetadata($this->metadata);
        }
        return $this->metadata;
    }
}
