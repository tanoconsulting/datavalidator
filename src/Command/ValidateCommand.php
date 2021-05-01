<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;
use TanoConsulting\DataValidatorBundle\ValidatorBuilder;
use TanoConsulting\DataValidatorBundle\Event\BeforeConstraintValidatedEvent;
use TanoConsulting\DataValidatorBundle\Logger\ConsoleLogger;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\LoaderInterface;

abstract class ValidateCommand extends Command
{
    protected $eventDispatcher;
    protected $taggedServicesLoader;
    protected $logger;
    protected $echoConstraintExecution = true;

    public function __construct(EventDispatcherInterface $eventDispatcher = null, LoaderInterface $taggedServicesLoader = null, LoggerInterface $datavalidatorLogger = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->taggedServicesLoader = $taggedServicesLoader;
        $this->logger = $datavalidatorLogger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('config-file', null, InputOption::VALUE_REQUIRED, 'A yaml/json file defining the constraints to check. If omitted: load them from config/services')
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
        $start = microtime(true);

        /// @todo get a standard logger injected via the constructor, push it into the ConsoleLogger
        $this->setLogger(new ConsoleLogger($output));

        $validatorBuilder = $this->getValidatorBuilder();

        if ($configFile = $input->getOption('config-file')) {
            $validatorBuilder->addFileMapping($configFile);
        } else {
            $validatorBuilder->addLoader($this->taggedServicesLoader);
        }

        $validatorBuilder->setEventDispatcher($this->eventDispatcher);

        $validationTarget = $this->getValidationTarget($input);

        $validator = $validatorBuilder->getValidator();

        $constraintsNum = count($validator->getConstraints());
        if ($constraintsNum) {
            $this->logger->notice('Found ' . count($validator->getConstraints()) . ' constraints to validate...');
        } else {
            $this->logger->error('Found no constraints to validate');
            return Command::FAILURE;
        }

        if (function_exists('pcntl_signal'))
        {
            pcntl_signal(SIGTERM, [$validator, 'onStopSignal']);
            pcntl_signal(SIGINT, [$validator, 'onStopSignal']);
        }

        /// @todo give signs of life during validation by writing something to stdout (stderr?)
        $violations = $validator->validate($validationTarget);

        $rows = [];
        if ($input->getOption('dry-run')) {
            $tableHeaders = ['Constraint', 'Details'];
            /** @var \TanoConsulting\DataValidatorBundle\ConstraintViolation $violation */
            foreach($violations as $violation) {
                $rows[] = [$violation->getConstraint()->getName(), $violation->getMessage()];
            }

        } elseif($input->getOption('display-data')) {
            /// @todo improve output: display as well FK defs/sql query ?

            $tableHeaders = ['Constraint', 'Violation', 'Data'];
            /** @var \TanoConsulting\DataValidatorBundle\ConstraintViolation $violation */
            foreach($violations as $violation) {
                $data = $violation->getInvalidValue();
                if (is_array($data)) {
                    foreach ($data as $i => $value) {
                        $rows[] = [$violation->getConstraint()->getName(), $i+1, preg_replace('/([\n\r] *)+/', ' ', json_encode($value))];
                    }
                } else {
                    // exceptions and similar...
                    $rows[] = [$violation->getConstraint()->getName(), 1, $violation->getMessage()];
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

        // print time taken and memory used
        $time = microtime(true) - $start;
        $this->logger->notice('Done.  Time taken: ' . sprintf('%.1f', $time) . ' secs, Max mem usage: ' .
            memory_get_peak_usage(true) . ' bytes'
        );

        // for dry run mode, the error is when there are no constraints defined...
        return (count($rows) xor $input->getOption('dry-run')) ? Command::FAILURE : Command::SUCCESS;
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

    /**
     * @return ValidatorBuilder
     */
    abstract protected function getValidatorBuilder();

    /**
     * @param InputInterface $input
     * @return mixed
     */
    abstract protected function getValidationTarget($input);
}
