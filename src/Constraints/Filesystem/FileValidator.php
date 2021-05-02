<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Filesystem;

use TanoConsulting\DataValidatorBundle\Constraints\FilesystemConstraint;
use TanoConsulting\DataValidatorBundle\Constraints\FilesystemValidator;

abstract class FileValidator extends FilesystemValidator
{
    protected function getFinder(FilesystemConstraint $constraint)
    {
        $finder = parent::getFinder($constraint);
        return $finder->files();
    }
}
