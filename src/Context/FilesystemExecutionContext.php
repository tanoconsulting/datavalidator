<?php

namespace TanoConsulting\DataValidatorBundle\Context;

class FilesystemExecutionContext extends ExecutionContext
{
    /**
     * @param int $operatingMode
     */
    public function __construct($operatingMode = self::MODE_COUNT)
    {
        $this->operatingMode = $operatingMode;

        parent::__construct();
    }
}
