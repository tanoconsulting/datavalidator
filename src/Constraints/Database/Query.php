<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use TanoConsulting\DataValidatorBundle\Constraints\DatabaseConstraint;

class Query extends DatabaseConstraint
{
    static protected $defaultName = 'QUERY_';

    public $sql;

    //public $message = 'This value is not valid.';

    /**
     * {@inheritdoc}
     *
     * @param string|array $sql The pattern to evaluate or an array of options
     */
    public function __construct(
        string $sql,
        string $name = null
        /*string $message = null,
        array $groups = null,
        $payload = null,
        array $options = []*/
    ) {
        $this->sql = $sql;
        $this->name = $name;
        /*if (\is_array($sql)) {
            $options = array_merge($sql, $options);
        } elseif (null !== $sql) {
            $options['value'] = $sql;
        }

        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;*/
    }

    /*public function getDefaultOption()
    {
        return 'sql';
    }

    public function getRequiredOptions()
    {
        return ['sql'];
    }*/
}
