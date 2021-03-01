<?php

namespace TanoConsulting\DataValidatorBundle\Context;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DatabaseExecutionContext extends ExecutionContext
{
    protected $connection;

    /**
     * DatabaseExecutionContext constructor.
     * @param string|Connection $connection string format: 'mysql://user:secret@localhost/mydb'
     * @param int $operatingMode
     */
    public function __construct($connection, $operatingMode = self::MODE_COUNT)
    {
        $this->connection = $connection;
        $this->operatingMode = $operatingMode;

        parent::__construct();
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        if (is_string($this->connection)) {
            // lazy connect to the db
            $this->connection = DriverManager::getConnection(['url' => $this->connection]);
        }

        return $this->connection;
    }
}
