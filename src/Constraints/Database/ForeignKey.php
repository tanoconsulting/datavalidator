<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use TanoConsulting\DataValidatorBundle\Constraints\DatabaseConstraint;

/// @todo in constructor validate contents of from/to/except members
class ForeignKey extends DatabaseConstraint
{
    static protected $defaultName = 'FK_';

    public $from;

    public $to;

    public $except;

    public function getRequiredOptions()
    {
        return ['from', 'to'];
    }
}
