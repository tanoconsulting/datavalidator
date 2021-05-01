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

    static protected $constraintsIndex = [];
    static protected $defaultName = 'DB_CONSTRAINT_';

    protected $name;

    public function getTargets()
    {
        return self::DATABASE_CONSTRAINT;
    }

    public function __construct($options = null /*, array $groups = null, $payload = null*/)
    {
        parent::__construct($options);
        if ($this->name === null) {
            if (!isset(self::$constraintsIndex[static::$defaultName])) {
                self::$constraintsIndex[static::$defaultName] = 1;
            }
            $this->name = static::$defaultName . self::$constraintsIndex[static::$defaultName];
            self::$constraintsIndex[static::$defaultName]++;
        }
    }

    public function getName()
    {
        return $this->name;
    }
}
