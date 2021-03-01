<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TanoConsulting\DataValidatorBundle\Context\DatabaseExecutionContext;
use TanoConsulting\DataValidatorBundle\DatabaseValidatorBuilder;

class ValidateDatabaseCommand extends Command
{
    protected static $defaultName = 'datavalidator:validate:database';
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Validates data in the database against a set of constraints')
            ->addOption('database', null, InputOption::VALUE_NONE, "The dsn of the database to connect to, eg: 'mysql://user:secret@localhost/mydb' or the doctrine connection name, eg, 'default'")
            ->addOption('schema-file', null, InputOption::VALUE_REQUIRED, 'A yaml/json file defining the constraints to check')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Only display the list of constraints')
            ->addOption('display-data', null, InputOption::VALUE_NONE, 'Display the offending table rows, not only their count')
            /// @todo allow filtering...
            //->addOption('omit-constraints', null, InputOption::VALUE_REQUIRED, 'A csv list of constraints not to check')
            //->addOption('only-constraints', null, InputOption::VALUE_REQUIRED, 'A csv list of constraints to check')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validatorBuilder =  new DatabaseValidatorBuilder();

        /// @todo check that $this->container is set before using it
        if ($connection = $input->getOption('database')) {
            if (strpos($connection, '://') === false) {
                $connection = $this->container->get('doctrine.dbal.'.$connection.'_connection');
            }
        } else {
            $connection = $this->container->get('doctrine.dbal.default_connection');
        }
        $validatorBuilder->setConnection($connection);

        if ($configFile = $input->getOption('config-file')) {
            $validatorBuilder->addFileMapping($configFile);
        }

        if ($input->getOption('dry-run')) {
            $validatorBuilder->setOperatingMode(DatabaseExecutionContext::MODE_DRY_RUN);
        } elseif($input->getOption('display-data')) {
            $validatorBuilder->setOperatingMode(DatabaseExecutionContext::MODE_FETCH);
        }

        $validator = $validatorBuilder->getValidator();
        /// @todo give signs of life during validation by writing something to stdout (stderr?)
        /// @todo catch ctrl-c and do a nice shutdown
        $violations = $validator->validate();

        $rows = [];
        if ($input->getOption('dry-run')) {
            $validatorBuilder->setOperatingMode(DatabaseExecutionContext::MODE_DRY_RUN);
            $tableHeaders = ['Constraint', 'Details'];
            /** @var \TanoConsulting\DataValidatorBundle\ConstraintViolation $violation */
            foreach($violations as $violation) {
                $rows[] = [$violation->getConstraint()->getName(), $violation->getMessage()];
            }
        } elseif($input->getOption('display-data')) {
            $validatorBuilder->setOperatingMode(DatabaseExecutionContext::MODE_FETCH);
            $tableHeaders = ['Constraint', 'Violation', 'Data'];
            /** @var \TanoConsulting\DataValidatorBundle\ConstraintViolation $violation */
            foreach($violations as $violation) {
                $data = $violation->getInvalidValue();
                foreach ($data as $i => $value) {
                    $rows[] = [$violation->getConstraint()->getName(), $i, preg_replace('/([\n\r] *)+/', ' ', json_encode($data))];
                }
            }
        } else {
            $tableHeaders = ['Constraint', 'Violations', 'Details'];
            /** @var \TanoConsulting\DataValidatorBundle\ConstraintViolation $violation */
            foreach($violations as $violation) {
                $rows[] = [$violation->getConstraint()->getName(), $violation->getInvalidValue(), $violation->getMessage()];
            }
        }
        unset($violations);

        $table = new Table($output);
        $table->setHeaders($tableHeaders);
        $table->setRows($rows);
        $table->setColumnMaxWidth(3, 120);
        $table->render();

        // for dry run mode, the error is when there are no constraints defined...
        return (count($rows) xor $input->getOption('dry-run')) ? Command::FAILURE : Command::SUCCESS;
    }
}
