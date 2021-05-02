<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Database;

use TanoConsulting\DataValidatorBundle\Constraints\DatabaseConstraint;

class Query extends DatabaseConstraint
{
    public $sql;

    /**
     * @var null|array When not null, the query will be skipped if the reuired db asset is missing. Format:
     *                 'table' => 'emp' or 'table' => ['emp', 'dept']
     */
    public $requires;

    //public $message = 'This value is not valid.';

    public function getDefaultOption()
    {
        return 'sql';
    }

    public function getRequiredOptions()
    {
        return ['sql'];
    }
}
