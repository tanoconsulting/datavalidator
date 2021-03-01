<?php

namespace TanoConsulting\DataValidatorBundle;

/// @todo ...
interface ConstraintViolationInterface
{
    /**
     * Returns the violation message.
     *
     * @return string|\Stringable The violation message as a string or a stringable object
     */
    public function getMessage();
}
