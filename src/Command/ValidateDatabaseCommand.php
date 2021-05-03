<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TanoConsulting\DataValidatorBundle\ContainerConstraintValidatorFactory;
use TanoConsulting\DataValidatorBundle\DatabaseValidatorBuilder;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Database\TaggedServiceLoader;

class ValidateDatabaseCommand extends ValidateCommand
{
    protected static $defaultName = 'datavalidator:validate:database';
    protected $container;

    public function __construct(EventDispatcherInterface $eventDispatcher = null, TaggedServiceLoader $taggedServicesLoader = null,
        ContainerConstraintValidatorFactory $constraintValidatorFactory, LoggerInterface $datavalidatorLogger = null, ContainerInterface $container = null)
    {
        $this->container = $container;

        parent::__construct($eventDispatcher, $taggedServicesLoader, $constraintValidatorFactory, $datavalidatorLogger);
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Validates data in the database against a set of constraints')
            ->addOption('database', null, InputOption::VALUE_REQUIRED, "The dsn of the database to connect to, eg: 'mysql://user:secret@localhost/mydb' or the doctrine connection name, eg, 'default'")
            ->addOption('config-file', null, InputOption::VALUE_REQUIRED, 'A yaml/json file defining the constraints to check. If omitted: load them from config/services')
        ;
    }

    protected function getValidatorBuilder($input)
    {
        $validatorBuilder = new DatabaseValidatorBuilder();

        if ($configFile = $input->getOption('config-file')) {
            $validatorBuilder->addFileMapping($configFile);
        } else {
            $validatorBuilder->addLoader($this->taggedServicesLoader);
        }

        return $validatorBuilder;
    }

    protected function getValidationTarget($input)
    {
        /// @todo check that $this->container is set before using it
        if ($connection = $input->getOption('database')) {
            if (strpos($connection, '://') === false) {
                $connection = $this->container->get('doctrine.dbal.'.$connection.'_connection');
            }
        } else {
            $connection = $this->container->get('doctrine.dbal.default_connection');
        }

        return $connection;
    }
}
