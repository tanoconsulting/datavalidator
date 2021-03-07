<?php

namespace TanoConsulting\DataValidatorBundle\Context;

class DatabaseExecutionContextFactory implements ExecutionContextFactoryInterface
{
    protected $operatingMode;

    public function __construct($operatingMode = DatabaseExecutionContext::MODE_COUNT)
    {
        $this->operatingMode = $operatingMode;
    }

    public function createContext($validator)
    {
        return new DatabaseExecutionContext($this->operatingMode);
    }
}
