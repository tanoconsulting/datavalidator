<?php

namespace TanoConsulting\DataValidatorBundle\Context;

class DatabaseExecutionContextFactory implements ExecutionContextFactoryInterface
{
    protected $connection;
    protected $operatingMode;

    public function __construct($dsnOrConnection, $operatingMode = DatabaseExecutionContext::MODE_COUNT)
    {
        $this->connection = $dsnOrConnection;
        $this->operatingMode = $operatingMode;
    }

    public function createContext($validator)
    {
        return new DatabaseExecutionContext($this->connection, $this->operatingMode);
    }
}
