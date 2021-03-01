<?php

namespace TanoConsulting\DataValidatorBundle\Mapping;

use TanoConsulting\DataValidatorBundle\Constraint;

class DatabaseMetaData extends Metadata
{
    /**
     * @param Constraint $constraint
     * @return DatabaseMetaData
     */
    public function addConstraint(Constraint $constraint)
    {
        /// @todo throw for unsupported / hard-to-handle constraints ?

        return parent::addConstraint($constraint);
    }
}
