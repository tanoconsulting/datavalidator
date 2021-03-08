<?php

namespace TanoConsulting\DataValidatorBundle\Validator;

use TanoConsulting\DataValidatorBundle\ConstraintValidatorFactoryInterface;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextFactoryInterface;
use TanoConsulting\DataValidatorBundle\Mapping\Factory\MetadataFactoryInterface;
use TanoConsulting\DataValidatorBundle\Event\BeforeConstraintValidatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class Validator implements ValidatorInterface
{
    /** @var ExecutionContextFactoryInterface */
    protected $executionContextFactory;
    /** @var MetadataFactoryInterface */
    protected $metadataFactory;
    /** @var ConstraintValidatorFactoryInterface */
    protected $validatorFactory;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    protected $shouldStop = false;

    /**
     * @param $executionContextFactory
     * @param $metadataFactory
     * @param $validatorFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct($executionContextFactory, $metadataFactory, $validatorFactory, $eventDispatcher = null)
    {
        $this->executionContextFactory = $executionContextFactory;
        $this->metadataFactory = $metadataFactory;
        $this->validatorFactory = $validatorFactory;
        $this->eventDispatcher = $eventDispatcher;
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

            if ($this->eventDispatcher) {
                $event = new BeforeConstraintValidatedEvent($constraint);
                $this->eventDispatcher->dispatch($event, BeforeConstraintValidatedEvent::NAME);

                if ($event->isValidationCancelled()) {
                    continue;
                }
            }

            $constraintValidator = $this->validatorFactory->getInstance($constraint);
            $constraintValidator->initialize($context);
            $constraintValidator->validate($value, $constraint);

            /// @todo emit an 'after' validation event, allowing to recap the total constraints validated plus time taken
        }
        return $context->getViolations();
    }

    public function onStopSignal()
    {
        $this->shouldStop = true;
    }

    public function getConstraints()
    {
        return $this->metadataFactory->getMetadata()->getConstraints();
    }
}
