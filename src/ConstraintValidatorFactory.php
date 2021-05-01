<?php

namespace TanoConsulting\DataValidatorBundle;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
    protected $validators = [];

    /**
     * @param Constraint $constraint
     * @return ConstraintValidatorInterface
     */
    public function getInstance(Constraint $constraint)
    {
        $className = $constraint->validatedBy();

        if (!isset($this->validators[$className])) {
            $this->validators[$className] = new $className();
        }

        /// @todo throw if validator does not support the correct interface

        return $this->validators[$className];
    }
}
