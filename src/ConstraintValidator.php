<?php

namespace TanoConsulting\DataValidatorBundle;

use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;

abstract class ConstraintValidator implements ConstraintValidatorInterface
{
    /** @var ExecutionContextInterface $context */
    protected $context;

    /**
     * Initializes the constraint validator.
     * @param ExecutionContextInterface $context
     */
    public function initialize(ExecutionContextInterface $context)
    {
        //if (! $context instanceof \TanoConsulting\DataValidatorBundle\Context\DatabaseExecutionContext) {
        //    throw new \TypeError(self::class . ' validators can only use a DatabaseExecutionContext');
        //}

        $this->context = $context;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param Constraint $constraint
     */
    abstract public function validate(Constraint $constraint);
}
