<?php

namespace TanoConsulting\DataValidatorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateFilesystemCommand extends Command
{
    protected static $defaultName = 'datavalidator:validate:filesystem';

    protected function configure()
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /// @todo ...
        throw new \Exception('Not implemented yet');

        return Command::SUCCESS;
    }
}
