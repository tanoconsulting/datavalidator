<?php

namespace TanoConsulting\DataValidatorBundle\Constraints;

use TanoConsulting\DataValidatorBundle\Constraint;

abstract class FilesystemConstraint extends Constraint
{
    //public const FILE_CONSTRAINT = 'file';

    public const DIRECTORY_CONSTRAINT = 'directory';

    public function getTargets()
    {
        return self::DIRECTORY_CONSTRAINT;
    }
}
