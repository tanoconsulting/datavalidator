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
     * @return \TanoConsulting\DataValidatorBundle\ConstraintViolationListInterface
     */
    public function validate()
    {
        $context = $this->executionContextFactory->createContext($this);
        foreach($this->getConstraints() as $name => $constraint) {
            $validator = $this->validatorFactory->getInstance($constraint);
            $validator->initialize($context);
            $validator->validate($constraint);
        }
        return $context->getViolations();
    }

    protected function getConstraints()
    {
        return $this->metadataFactory->getMetadata()->getConstraints();
    }
}
