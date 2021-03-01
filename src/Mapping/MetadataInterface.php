<?php

namespace TanoConsulting\DataValidatorBundle\Mapping;

use TanoConsulting\DataValidatorBundle\Constraint;

interface MetadataInterface
{
    /**
     * Returns all constraints of this element.
     *
     * @return Constraint[] A list of Constraint instances
     */
    public function getConstraints();
}
