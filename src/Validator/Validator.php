<?php

namespace TanoConsulting\DataValidatorBundle\Validator;

use TanoConsulting\DataValidatorBundle\ConstraintValidatorFactoryInterface;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextFactoryInterface;
use TanoConsulting\DataValidatorBundle\Mapping\Factory\MetadataFactoryInterface;

abstract class Validator implements ValidatorInterface
{
    /** @var ExecutionContextFactoryInterface */
    protected $executionContextFactory;
    /** @var MetadataFactoryInterface */
    protected $metadataFactory;
    /** @var ConstraintValidatorFactoryInterface */
    protected $validatorFactory;

    protected $shouldStop = false;

    /**
     * @param $executionContextFactory
     * @param $metadataFactory
     * @param $validatorFactory
     */
    public function __construct($executionContextFactory, $metadataFactory, $validatorFactory)
    {
        $this->executionContextFactory = $executionContextFactory;
        $this->metadataFactory = $metadataFactory;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param mixed $value The value to validate
     * @return \TanoConsulting\DataValidatorBundle\ConstraintViolationListInterface
     */
    public function validate($value)
    {
        $context = $this->executionContextFactory->createContext($this);
        foreach($this->getConstraints() as $name => $constraint) {
            // allow exiting halfway through the loop
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
            if ( $this->shouldStop ) {
                break;
            }

            $constraintValidator = $this->validatorFactory->getInstance($constraint);
            $constraintValidator->initialize($context);
            $constraintValidator->validate($value, $constraint);
        }
        return $context->getViolations();
    }

    public function onStopSignal()
    {
        $this->shouldStop = true;
    }

    protected function getConstraints()
    {
        return $this->metadataFactory->getMetadata()->getConstraints();
    }
}
