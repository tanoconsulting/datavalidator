<?php

namespace TanoConsulting\DataValidatorBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TanoConsulting\DataValidatorBundle\Constraint;

class BeforeConstraintValidatedEvent extends Event
{
    public const NAME = 'before.constraint.validation';

    protected $constraint;
    protected $validationCancelled = false;

    public function __construct(Constraint $constraint)
    {
        $this->constraint = $constraint;
    }

    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * @return bool
     */
    public function isValidationCancelled()
    {
        return $this->validationCancelled;
    }

    /**
     * Cancels the validation of this constraint
     */
    public function cancelValidation()
    {
        $this->validationCancelled = true;
    }
}
