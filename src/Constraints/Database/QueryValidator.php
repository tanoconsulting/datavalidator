<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use Doctrine\DBAL\Connection;
use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\ConstraintValidator;
use TanoConsulting\DataValidatorBundle\ConstraintViolation;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;

class QueryValidator extends ConstraintValidator
{
    /**
     * @param Query $constraint
     * @throws \Doctrine\DBAL\Exception
     */
    public function validate(Constraint $constraint)
    {
        /** @var Connection $connection */
        $connection = $this->context->getConnection();

        switch($this->context->getOperatingMode()) {
            case ExecutionContextInterface::MODE_COUNT:
                $violationCount = $connection->executeQuery('SELECT COUNT(*) AS rows FROM (' . rtrim($constraint->sql, ';') . ') subquery')->fetchOne();
                $this->context->addViolation(new ConstraintViolation($constraint->sql, $violationCount, $constraint));
                break;
            case ExecutionContextInterface::MODE_FETCH:
                $violationData = $connection->executeQuery($constraint->sql)->fetchAllAssociative();
                $this->context->addViolation(new ConstraintViolation($constraint->sql, $violationData, $constraint));
                break;
            case ExecutionContextInterface::MODE_DRY_RUN:
                $this->context->addViolation(new ConstraintViolation($constraint->sql, null, $constraint));
                break;
        }
    }
}
