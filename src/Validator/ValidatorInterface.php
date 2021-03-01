<?php

namespace TanoConsulting\DataValidatorBundle\Validator;

use TanoConsulting\DataValidatorBundle\ConstraintViolationListInterface;

interface ValidatorInterface
{
    /**
     * @return ConstraintViolationListInterface A list of constraint violations. If the list is empty, validation
     *                                          succeeded
     */
    public function validate();
}
