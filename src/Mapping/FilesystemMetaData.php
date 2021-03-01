<?php

namespace TanoConsulting\DataValidatorBundle\Mapping;

use TanoConsulting\DataValidatorBundle\Constraint;

class FilesystemMetaData extends Metadata
{
    /**
     * @param Constraint $constraint
     * @return FilesystemMetaData
     */
    public function addConstraint(Constraint $constraint)
    {
        /// @todo throw for unsupported / hard-to-handle constraints ?

        return parent::addConstraint($constraint);
    }
}
