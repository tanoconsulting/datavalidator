<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader;

use TanoConsulting\DataValidatorBundle\Mapping\Metadata;

interface LoaderInterface
{
    public function loadMetadata(Metadata $metadata);
}
