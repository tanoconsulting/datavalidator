<?php

namespace TanoConsulting\DataValidatorBundle;

use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;

interface ConstraintValidatorInterface
{
    /**
     * Initializes the constraint validator.
     *
     * @param ExecutionContextInterface $context
     */
    public function initialize(ExecutionContextInterface $context);

    /**
     * Checks if the passed constraint is valid.
     *
     * @param mixed $value The value that should be validate
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint);
}
