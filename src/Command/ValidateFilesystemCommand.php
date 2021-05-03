<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $this
            ->setDescription('Validates data in the filesystem against a set of constraints')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, "The root path to start scanning eg: '/var/my_data'. If not specified, the current directory is used")
            ->addOption('config-file', null, InputOption::VALUE_REQUIRED, 'A yaml/json file defining the constraints to check. If omitted: load them from config/services')
        ;
    }

    protected function getValidatorBuilder($input)
    {
        $validatorBuilder = new FilesystemValidatorBuilder();

        if ($configFile = $input->getOption('config-file')) {
            $validatorBuilder->addFileMapping($configFile);
        } else {
            $validatorBuilder->addLoader($this->taggedServicesLoader);
        }

        return $validatorBuilder;
    }

    protected function getValidationTarget($input)
    {
        $path = $input->getOption('path');
        if ($path === null || $path === '') {
            $path = getcwd();
        }

        $path = realpath($path);

        /// @todo we should check that $path is a directory

        return $path;
    }
}
