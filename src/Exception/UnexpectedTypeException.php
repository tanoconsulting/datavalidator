<?php

namespace TanoConsulting\DataValidatorBundle\Exception;

class UnexpectedTypeException extends ValidatorException
{
    public function __construct($value, string $expectedType)
    {
        parent::__construct(sprintf('Expected argument of type "%s", "%s" given', $expectedType, get_class($value)));
    }
}
