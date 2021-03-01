<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Factory;

use TanoConsulting\DataValidatorBundle\Mapping\FilesystemMetaData;

class FilesystemMetadataFactory extends MetadataFactory implements MetadataFactoryInterface
{
    public function getMetadata()
    {
        $metadata = new FilesystemMetaData();
        $this->loader->loadMetadata($metadata);
        return $metadata;
    }
}
