<?php

namespace TanoConsulting\DataValidatorBundle\Constraints;

use TanoConsulting\DataValidatorBundle\Constraint;

abstract class FilesystemConstraint extends Constraint
{
    public const FILESYSTEM_CONSTRAINT = 'filesystem';
    //public const FILE_CONSTRAINT = 'file';
    //public const DIRECTORY_CONSTRAINT = 'directory';

    static protected $constraintsIndex = [];
    static protected $defaultName = 'filesystem_constraint';
    static protected $targets = self::FILESYSTEM_CONSTRAINT;

    protected $name;

    /** @var null|array[] $filter each element of the array is of the type: [criterion => value]  */
    public $filter;

    public function getTargets()
    {
        return self::$targets;
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
