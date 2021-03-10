<?php

namespace TanoConsulting\DataValidatorBundle\Exception;

use Throwable;

class InvalidOptionsException extends ValidatorException
{
    protected $options;

    public function __construct(string $message, array $options, Throwable $previous = NULL)
    {
        parent::__construct($message, 0, $previous);

        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
