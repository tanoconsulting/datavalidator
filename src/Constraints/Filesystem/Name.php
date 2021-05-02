<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Filesystem;

use TanoConsulting\DataValidatorBundle\Constraints\FilesystemConstraint;

class Name extends FilesystemConstraint
{
    /** @var string $matches a regular expression */
    public $matches;

    public function getDefaultOption()
    {
        return 'matches';
    }

    public function getRequiredOptions()
    {
        return ['matches'];
    }
}
