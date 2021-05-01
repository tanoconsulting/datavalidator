<?php

namespace TanoConsulting\DataValidatorBundle;

use Psr\Container\ContainerInterface;
use TanoConsulting\DataValidatorBundle\Exception\UnexpectedTypeException;
use TanoConsulting\DataValidatorBundle\Exception\ValidatorException;

/**
 * Uses a service container to create constraint validators.
 */
class ContainerConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
    private $container;
    private $validators;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validators = [];
    }

    /**
     * @throws ValidatorException      When the validator class does not exist
     * @throws UnexpectedTypeException When the validator is not an instance of ConstraintValidatorInterface
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if (!isset($this->validators[$name])) {
            if ($this->container->has($name)) {
                $this->validators[$name] = $this->container->get($name);
            } else {
                if (!class_exists($name)) {
                    throw new ValidatorException(sprintf('Constraint validator "%s" does not exist or is not enabled. Check the "validatedBy" method in your constraint class "%s".', $name, get_debug_type($constraint)));
                }

                $this->validators[$name] = new $name();
            }
        }

        if (!$this->validators[$name] instanceof ConstraintValidatorInterface) {
            throw new UnexpectedTypeException($this->validators[$name], ConstraintValidatorInterface::class);
        }

        return $this->validators[$name];
    }
}
