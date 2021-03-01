<?php

namespace TanoConsulting\DataValidatorBundle\Constraints;

use TanoConsulting\DataValidatorBundle\Constraint;

abstract class DatabaseConstraint extends Constraint
{
    /**
     * Marks a constraint that can be put onto cross-table data.
     */
    public const DATABASE_CONSTRAINT = 'database';

    /**
     * Marks a constraint that can be put onto table column data.
     */
    //public const DATABASE_COLUMN_CONSTRAINT = 'database_column';

    static protected $constraintsIndex = 1;
    static protected $defaultName = 'DB_CONSTRAINT_';

    protected $name;

    public function getTargets()
    {
        return self::DATABASE_CONSTRAINT;
    }

    public function getName()
    {
        if ($this->name === null) {
            $this->name = static::$defaultName . static::$constraintsIndex;
            self::$constraintsIndex++;
        }
        return $this->name;
    }
}
