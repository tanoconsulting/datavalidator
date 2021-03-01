<?php

namespace TanoConsulting\DataValidatorBundle;

/// @todo ...
class ConstraintViolation implements ConstraintViolationInterface
{
    protected $message;
    protected $constraint;
    protected $invalidValue;

    public function __construct($message, $invalidValue, $constraint = null)
    {
        $this->message = $message;
        $this->invalidValue = $invalidValue;
        $this->constraint = $constraint;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getInvalidValue()
    {
        return $this->invalidValue;
    }

    /**
     * Returns the constraint whose validation caused the violation.
     *
     * @return Constraint|null The constraint or null if it is not known
     */
    public function getConstraint()
    {
        return $this->constraint;
    }
}
