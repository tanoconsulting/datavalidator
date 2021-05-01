<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use Doctrine\DBAL\Connection;
use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\Constraints\DatabaseValidator;
use TanoConsulting\DataValidatorBundle\ConstraintViolation;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;
use TanoConsulting\DataValidatorBundle\Exception\ConstraintDefinitionException;

class QueryValidator extends DatabaseValidator
{
    /**
     * @param string|Connection $value string format: 'mysql://user:secret@localhost/mydb'
     * @param Constraint $constraint
     * @throws \Doctrine\DBAL\Exception
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var Connection $connection */
        $connection = $this->getConnection($value);

        switch($this->context->getOperatingMode()) {
            case ExecutionContextInterface::MODE_COUNT:
                if ($this->shouldSkipConstraint($constraint, $connection))
                {
                    /// @todo emit a warning
                    return;
                }
                try {
                    $violationCount = $connection->executeQuery('SELECT COUNT(*) AS numrows FROM (' . rtrim($constraint->sql, ';') . ') subquery')->fetchOne();
                    if ($violationCount) {
                        $this->context->addViolation(new ConstraintViolation($constraint->sql, $violationCount, $constraint));
                    }
                } catch (\Exception $e) {
                    $this->context->addViolation(new ConstraintViolation(preg_replace('/\n */', ' ', $e->getMessage()), null, $constraint));
                }
                break;
            case ExecutionContextInterface::MODE_FETCH:
                if ($this->shouldSkipConstraint($constraint, $connection))
                {
                    /// @todo emit a warning
                    return;
                }
                try {
                    $violationData = $connection->executeQuery($constraint->sql)->fetchAllAssociative();
                    if ($violationData) {
                        $this->context->addViolation(new ConstraintViolation($constraint->sql, $violationData, $constraint));
                    }
                } catch (\Exception $e) {
                    $this->context->addViolation(new ConstraintViolation(preg_replace('/\n */', ' ', $e->getMessage()), null, $constraint));
                }
                break;
            case ExecutionContextInterface::MODE_DRY_RUN:
                $this->context->addViolation(new ConstraintViolation($constraint->sql, null, $constraint));
                break;
        }
    }

    /**
     * @param Constraint $constraint $constraint
     * @param Connection $connection
     * @return bool
     * @throws ConstraintDefinitionException
     */
    protected function shouldSkipConstraint($constraint, $connection)
    {
        if ($constraint->requires === null) {
            return false;
        }

        $targets = (array) reset($constraint->requires);
        $type = key($constraint->requires);

        switch($type) {
            case 'table':
                $this->analyzeSchema($connection);
                foreach ($targets as $target) {
                    if (!isset(static::$tables[$target])) {
                        return true;
                    }
                }
                break;
            default:
                throw new ConstraintDefinitionException("Can not check for existence of required db asset of type: $type");
        }

        return false;
    }
}
