<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TanoConsulting\DataValidatorBundle\Event\BeforeConstraintValidatedEvent;

abstract class ValidateCommand extends Command
{
    protected $eventDispatcher;
    protected $logger;
    protected $echoConstraintExecution = true;

    public function __construct(EventDispatcherInterface $eventDispatcher = null, LoggerInterface $datavalidatorLogger = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $datavalidatorLogger;

        parent::__construct();
    }

    public function onBeforeConstraintValidation(BeforeConstraintValidatedEvent $event)
    {
        if ($this->echoConstraintExecution) {
            $this->logger->notice('Validating constraint: ' . $event->getConstraint()->getName() . '...');
        }
    }

    protected function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
