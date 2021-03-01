<?php

namespace TanoConsulting\DataValidatorBundle;

/**
 * Specifies an object able to return the correct ConstraintValidatorInterface
 * instance given a Constraint object.
 */
interface ConstraintValidatorFactoryInterface
{
    /**
     * Given a Constraint, this returns the ConstraintValidatorInterface
     * object that should be used to verify its validity.
     *
     * @return ConstraintValidatorInterface
     */
    public function getInstance(Constraint $constraint);
}
