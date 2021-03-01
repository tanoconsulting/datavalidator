<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use Doctrine\DBAL\Connection;
use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\ConstraintValidator;
use TanoConsulting\DataValidatorBundle\ConstraintViolation;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;

class ForeignKeyValidator extends ConstraintValidator
{
    public function validate(Constraint $constraint)
    {
        /** @var Connection $connection */
        $connection = $this->context->getConnection();

        /// @todo ...
        switch($this->context->getOperatingMode()) {
            case ExecutionContextInterface::MODE_COUNT:
                //$violationCount = $connection->executeQuery('SELECT COUNT(*) AS rows FROM (' . rtrim($constraint->sql, ';') . ') subquery')->fetchOne();
                //$this->context->addViolation(new ConstraintViolation($constraint->sql, $violationCount, $constraint));
                break;
            case ExecutionContextInterface::MODE_FETCH:
                //$violationData = $connection->executeQuery($constraint->sql)->fetchAllAssociative();
                //$this->context->addViolation(new ConstraintViolation($constraint->sql, $violationData, $constraint));
                break;
            case ExecutionContextInterface::MODE_DRY_RUN:
                $message = preg_replace('/\n */', ' ', json_encode(
                    ['from' => $constraint->from, 'to' => $constraint->to, 'except' => $constraint->except])
                );
                $this->context->addViolation(new ConstraintViolation($message, null, $constraint));
                break;
        }
    }
}
