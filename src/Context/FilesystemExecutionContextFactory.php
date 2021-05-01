<?php

namespace TanoConsulting\DataValidatorBundle\Context;

class FilesystemExecutionContextFactory implements ExecutionContextFactoryInterface
{
    protected $operatingMode;

    public function __construct($operatingMode = FilesystemExecutionContext::MODE_COUNT)
    {
        $this->operatingMode = $operatingMode;
    }

    public function createContext($validator)
    {
        return new FilesystemExecutionContext($this->operatingMode);
    }
}
