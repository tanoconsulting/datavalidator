<?php

namespace TanoConsulting\DataValidatorBundle\Constraints;

use Symfony\Component\Finder\Finder;
use TanoConsulting\DataValidatorBundle\ConstraintValidator;
use TanoConsulting\DataValidatorBundle\Exception\ConstraintDefinitionException;

abstract class FilesystemValidator extends ConstraintValidator
{
    /**
     * @param FilesystemConstraint $constraint
     * @return Finder
     * @throws ConstraintDefinitionException
     */
    protected function getFinder(FilesystemConstraint $constraint)
    {
        $finder = new Finder();

        if ($constraint->filter !== null) {
            foreach ($constraint->filter as $filter) {
                $value = reset($filter);
                $criterion = key($filter);
                switch($criterion) {
                    case 'contains':
                    case 'date':
                    case 'depth':
                    case 'name':
                    case 'notName':
                    case 'notContains':
                    case 'size':
                        $finder->$criterion($value);
                        break;
                    default:
                        throw new ConstraintDefinitionException("Can not filter filesystem for validation based on criterion: $criterion");
                }
            }
        }

        return $finder;
    }
}
