<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TanoConsulting\DataValidatorBundle\ContainerConstraintValidatorFactory;
use TanoConsulting\DataValidatorBundle\FilesystemValidatorBuilder;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Filesystem\TaggedServiceLoader;

class ValidateFilesystemCommand extends ValidateCommand
{
    protected static $defaultName = 'datavalidator:validate:filesystem';

    public function __construct(EventDispatcherInterface $eventDispatcher = null, TaggedServiceLoader $taggedServicesLoader = null,
        ContainerConstraintValidatorFactory $constraintValidatorFactory, LoggerInterface $datavalidatorLogger = null)
    {
        parent::__construct($eventDispatcher, $taggedServicesLoader, $constraintValidatorFactory, $datavalidatorLogger);
    }

    protected function configure()
    {
        parent::configure();

        /// @todo...
    }

    protected function getValidatorBuilder()
    {
        return new FilesystemValidatorBuilder();
    }

    protected function getValidationTarget($input)
    {
        /// @todo...
    }
}
