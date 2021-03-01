<?php

namespace TanoConsulting\DataValidatorBundle\Context;

interface ExecutionContextFactoryInterface
{
    /**
     * Creates a new execution context.
     *
     * @param $validator
     * @return ExecutionContextInterface The new execution context
     */
    public function createContext($validator);
}
