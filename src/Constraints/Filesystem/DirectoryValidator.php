<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Filesystem;

use TanoConsulting\DataValidatorBundle\Constraints\FilesystemConstraint;
use TanoConsulting\DataValidatorBundle\Constraints\FilesystemValidator;

abstract class DirectoryValidator extends FilesystemValidator
{
    protected function getFinder(FilesystemConstraint $constraint)
    {
        $finder = parent::getFinder($constraint);
        return $finder->directories();
    }
}
