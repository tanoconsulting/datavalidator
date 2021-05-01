<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use TanoConsulting\DataValidatorBundle\Constraints\DatabaseConstraint;

/// @todo in constructor validate contents of members from/to/except
class ForeignKey extends DatabaseConstraint
{
    static protected $defaultName = 'foreign_key_';

    public $child;

    public $parent;

    public $except;

    /**
     * @return string[]
     */
    public function getRequiredOptions()
    {
        return ['child', 'parent'];
    }
}
