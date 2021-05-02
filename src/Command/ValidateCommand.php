<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TanoConsulting\DataValidatorBundle\ConstraintValidatorFactoryInterface;
use TanoConsulting\DataValidatorBundle\ConstraintViolation;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContext;
use TanoConsulting\DataValidatorBundle\Event\BeforeConstraintValidatedEvent;
use TanoConsulting\DataValidatorBundle\Logger\ConsoleLogger;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\LoaderInterface;
use TanoConsulting\DataValidatorBundle\ValidatorBuilder;

abstract class ValidateCommand extends Command
{
    protected $eventDispatcher;
    protected $taggedServicesLoader;
    protected $constraintValidatorFactory;
    protected $logger;
    protected $echoConstraintExecution = true;

    public function __construct(EventDispatcherInterface $eventDispatcher = null, LoaderInterface $taggedServicesLoader = null,
        ConstraintValidatorFactoryInterface $constraintValidatorFactory, LoggerInterface $datavalidatorLogger = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->taggedServicesLoader = $taggedServicesLoader;
        $this->constraintValidatorFactory = $constraintValidatorFactory;
        $this->logger = $datavalidatorLogger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('config-file', null, InputOption::VALUE_REQUIRED, 'A yaml/json file defining the constraints to check. If omitted: load them from config/services')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Only display the list of constraints')
            ->addOption('display-data', null, InputOption::VALUE_NONE, 'Display the offending data (table rows / files), not only their count')
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
        $validatorBuilder->setConstraintValidatorFactory($this->constraintValidatorFactory);

        $operatingMode = ExecutionContext::MODE_COUNT;
        if ($input->getOption('dry-run')) {
            $operatingMode = ExecutionContext::MODE_DRY_RUN;
        } else if ($input->getOption('display-data')) {
            $operatingMode = ExecutionContext::MODE_FETCH;
        }
        $validatorBuilder->setOperatingMode($operatingMode);

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

        $rows = $this->buildResultsTable($violations, $operatingMode);
        $tableHeaders = array_shift($rows);

        // save memory
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
        return (count($rows) xor $operatingMode === ExecutionContext::MODE_DRY_RUN) ? Command::FAILURE : Command::SUCCESS;
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
     * @param ConstraintViolation[] $violations
     * @param int $operatingMode
     * @return array[] First row are table headers
     */
    protected function buildResultsTable($violations, $operatingMode)
    {
        $rows = [];
        switch ($operatingMode) {
            case ExecutionContext::MODE_DRY_RUN:
                $rows = [['Constraint', 'Details']];

                foreach($violations as $violation) {
                    $rows[] = [$violation->getConstraint()->getName(), $violation->getMessage()];
                }
                break;

            case ExecutionContext::MODE_FETCH:
                /// @todo improve output: display as well the violation message ?

                $rows = [['Constraint', 'Violation', 'Data']];
                foreach($violations as $violation) {
                    $data = $violation->getInvalidValue();
                    /// @todo handle iterable $data the same way as we do arrays
                    if (is_array($data)) {
                        $i = 0;
                        foreach ($data as $value) {
                            $rows[] = [$violation->getConstraint()->getName(), ++$i, $this->toString($value)];
                        }
                    } else {
                        $rows[] = [$violation->getConstraint()->getName(), 1, $this->toString($data)];
                    }
                }
                break;

            case ExecutionContext::MODE_COUNT:
                $rows = [['Constraint', 'Violations', 'Details']];
                // we expect the violation value to always be an integer
                foreach($violations as $violation) {
                    $rows[] = [$violation->getConstraint()->getName(), $violation->getInvalidValue(), $violation->getMessage()];
                }
                break;

            /// @todo break on unsupported modes
        }

        return $rows;
    }

    /**
     * Used for building a string representation of violation data
     * @param mixed $value
     * @return float|int|string
     */
    protected function toString($value)
    {
        if ($value instanceof \Throwable) {
            $value = $value->getMessage();
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        return preg_replace('/([\n\r] *)+/', ' ', json_encode($value));
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
