<?php

namespace TanoConsulting\DataValidatorBundle\Mapping;

use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\Constraints\DatabaseConstraint;
use TanoConsulting\DataValidatorBundle\Exception\ConstraintDefinitionException;

class DatabaseMetaData extends Metadata
{
    /**
     * @param Constraint $constraint
     * @return DatabaseMetaData
     * @throws ConstraintDefinitionException
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->checkConstraint($constraint);

        return parent::addConstraint($constraint);
    }

    protected function checkConstraint(Constraint $constraint)
    {
        if (!\in_array(DatabaseConstraint::DATABASE_CONSTRAINT, (array) $constraint->getTargets(), true)) {
            throw new ConstraintDefinitionException(sprintf('The constraint "%s" cannot be put on databases.', get_debug_type($constraint)));
        }

        //f ($constraint instanceof Composite) {
        //    foreach ($constraint->getNestedContraints() as $nestedContraint) {
        //        $this->checkConstraint($nestedContraint);
        //    }
        //}
    }
}
