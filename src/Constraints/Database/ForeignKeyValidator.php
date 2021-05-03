<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use Doctrine\DBAL\Connection;
use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\Constraints\DatabaseValidator;
use TanoConsulting\DataValidatorBundle\ConstraintViolation;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;
use TanoConsulting\DataValidatorBundle\Exception\UnexpectedTypeException;

class ForeignKeyValidator extends DatabaseValidator
{
    /**
     * @param string|Connection $value string format: 'mysql://user:secret@localhost/mydb'
     * @param Constraint $constraint
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ForeignKey) {
            throw new UnexpectedTypeException($constraint, ForeignKey::class);
        }

        /** @var Connection $connection */
        $connection = $this->getConnection($value);

        /// @todo add verification of FK cols defs matching...

        switch($this->context->getOperatingMode()) {
            case ExecutionContextInterface::MODE_COUNT:
                // skip check if either table does not exist
                if ($this->shouldSkipConstraint($constraint, $connection))
                {
                    /// @todo emit a warning
                    return;
                }
                try {
                    $violationCount = $connection->executeQuery($this->getQuery($constraint, true))->fetchOne();
                    if ($violationCount) {
                        $this->context->addViolation(new ConstraintViolation($this->getMessage($constraint), $violationCount, $constraint));
                    }
                } catch (\Throwable $e) {
                    $this->context->addViolation(new ConstraintViolation($e->getMessage(), 1, $constraint));
                }
                break;

            case ExecutionContextInterface::MODE_FETCH:
                // skip check if either table does not exist
                if ($this->shouldSkipConstraint($constraint, $connection))
                {
                    /// @todo emit a warning
                    return;
                }
                try {
                    $violationData = $connection->executeQuery($this->getQuery($constraint))->fetchAllAssociative();
                    if ($violationData) {
                        $this->context->addViolation(new ConstraintViolation($this->getMessage($constraint), $violationData, $constraint));
                    }
                } catch (\Throwable $e) {
                    $this->context->addViolation(new ConstraintViolation($this->getMessage($constraint), $e, $constraint));
                }
                break;

            case ExecutionContextInterface::MODE_DRY_RUN:
                /// @todo simplify visualization and move this to the constraint itself
                $this->context->addViolation(new ConstraintViolation($this->getMessage($constraint), null, $constraint));
                break;
        }
    }

    protected function getMessage(ForeignKey $constraint)
    {
        $childTable = key($constraint->child);
        $parentTable = key($constraint->parent);
        $childCol = current($constraint->child);
        $parentCol = current($constraint->parent);
        $exceptions = $constraint->except;
        $childCols = [];
        foreach((array)$childCol as $col) {
            /// @todo allows for $col being a csv
            $childCols[] = $childTable . '.' . $col;
        }
        $parentCols = [];
        foreach((array)$parentCol as $col) {
            /// @todo allows for $col being a csv
            $parentCols[] = $parentTable . '.' . $col;
        }
        $out = implode(', ', $childCols) . ' => ' . implode(', ', $parentCols);
        if ($exceptions != null) {
            $out .= ' only when: ' . $exceptions;
        }
        return $out;
    }

    protected function getQuery($constraint, $count = false)
    {
        $childTable = key($constraint->child);
        $parentTable = key($constraint->parent);
        $childCol = current($constraint->child);
        $parentCol = current($constraint->parent);
        $exceptions = $constraint->except;

        if ($childTable == $parentTable) {
            $childTableFull = $childTable . " child";
            $parentTableFull = $parentTable . " parent";
            $exceptions = $this->rewriteExceptionsQueryFragment($exceptions, $childTable, $childCol, $parentTable, $parentCol);
            $childTable = 'child';
            $parentTable = 'parent';
        } else {
            $childTableFull = $childTable;
            $parentTableFull = $parentTable;
        }

        $sql =
            "SELECT " . ($count ? "COUNT(*) AS violations " : $this->escapeIdentifier($childTable) . ".* ") .
            "FROM " . $this->escapeIdentifier($childTableFull) . " " .
            "LEFT JOIN " . $this->escapeIdentifier($parentTableFull) . " " .
            "ON " . $this->getJoinQueryFragment($childTable, $childCol, $parentTable, $parentCol) . " " .
            "WHERE " . $this->getWhereQueryFragment($parentTable, $parentCol);
        if ($exceptions != null) {
            $sql .= ' AND ' . $exceptions;
        }

        return $sql;
    }

    /**
     * @param string $childTable
     * @param string|string[] $childCol
     * @param string $parentTable
     * @param string|string[] $parentCol
     * @return string
     */
    protected function getJoinQueryFragment($childTable, $childCol, $parentTable, $parentCol)
    {
        if (is_string($childCol)) {
            $childCols = explode(',', $childCol);
        } else {
            $childCols = $childCol;
        }
        if (is_string($parentCol)) {
            $parentCols = explode(',', $parentCol);
        } else {
            $parentCols = $parentCol;
        }

        $fragments = array();
        foreach ($childCols as $i => $childCol) {
            $fragments[] = $this->escapeIdentifier($childTable) . '.' . $this->escapeIdentifier($childCol) . " = " .
                $this->escapeIdentifier($parentTable) . "." . $this->escapeIdentifier($parentCols[$i]);
        }

        return join(' AND ', $fragments);
    }

    /**
     * @param string $parentTable
     * @param string|string[] $parentCol
     * @return string
     */
    protected function getWhereQueryFragment($parentTable, $parentCol)
    {
        if (is_string($parentCol)) {
            $parentCols = explode(',', $parentCol);
        } else {
            $parentCols = $parentCol;
        }

        $fragments = array();
        foreach ($parentCols as $i => $parentCol) {
            $fragments[] = $this->escapeIdentifier($parentTable) . "." . $this->escapeIdentifier($parentCol) . " IS NULL";
        }

        return join(' AND ', $fragments);
    }

    /**
     * @param string $exceptions
     * @param string $childTable
     * @param string|string[] $childCol
     * @param string $parentTable
     * @param string|string[] $parentCol
     * @return string
     */
    protected function rewriteExceptionsQueryFragment($exceptions, $childTable, $childCol, $parentTable, $parentCol)
    {
        if (is_string($childCol)) {
            $childCols = explode(',', $childCol);
        } else {
            $childCols = $childCol;
        }
        if (is_string($parentCol)) {
            $parentCols = explode(',', $parentCol);
        } else {
            $parentCols = $parentCol;
        }

        foreach ($childCols as $i => $childCol) {
            $exceptions = str_replace($childTable . '.' . $childCol, 'child.' . $childCol, $exceptions);
        }

        foreach ($parentCols as $i => $parentCol) {
            $exceptions = str_replace($parentTable . '.' . $parentCol, 'parent.' . $parentCol, $exceptions);
        }

        return $exceptions;
    }

    /// @todo
    protected function escapeIdentifier($name)
    {
        return $name;
    }

    /// @todo what about skipping if one column is missing ?
    protected function shouldSkipConstraint($constraint, $connection)
    {
        $childTable = key($constraint->child);
        $parentTable = key($constraint->parent);

        $this->analyzeSchema($connection);
        if (!isset(static::$tables[$childTable]) || !isset(static::$tables[$parentTable])) {
            return true;
        }

        return false;
    }
}
