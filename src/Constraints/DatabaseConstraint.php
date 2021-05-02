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

    static protected $targets = self::DATABASE_CONSTRAINT;

    protected $name;

    public function getTargets()
    {
        return static::$targets;
    }

    public function __construct($options = null /*, array $groups = null, $payload = null*/)
    {
        parent::__construct($options);

        if ($this->name === null) {
            /// @todo handle better the case of constraints from other bundles with same name as ours
            $className = implode('\\', array_slice(explode('\\', static::class), -2));
            if (!isset(self::$constraintsIndex[$className])) {
                self::$constraintsIndex[$className] = 1;
            }
            $this->name = $className . '::' . self::$constraintsIndex[$className];
            self::$constraintsIndex[$className]++;
        }
    }

    public function getName()
    {
        return $this->name;
    }
}
