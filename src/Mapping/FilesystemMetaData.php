<?php

namespace TanoConsulting\DataValidatorBundle\Mapping;

use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\Constraints\FilesystemConstraint;
use TanoConsulting\DataValidatorBundle\Exception\ConstraintDefinitionException;

class FilesystemMetaData extends Metadata
{
    /**
     * @param Constraint $constraint
     * @return FilesystemMetaData
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->checkConstraint($constraint);

        return parent::addConstraint($constraint);
    }

    protected function checkConstraint(Constraint $constraint)
    {
        $targets = (array) $constraint->getTargets();
        if (!\in_array(FilesystemConstraint::DIRECTORY_CONSTRAINT, $targets, true) && !\in_array(FilesystemConstraint::FILE_CONSTRAINT, $targets, true)) {
            throw new ConstraintDefinitionException(sprintf('The constraint "%s" cannot be put on filesystems.', get_debug_type($constraint)));
        }

        //f ($constraint instanceof Composite) {
        //    foreach ($constraint->getNestedContraints() as $nestedContraint) {
        //        $this->checkConstraint($nestedContraint);
        //    }
        //}
    }
}
