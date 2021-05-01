<?php

namespace TanoConsulting\DataValidatorBundle\Constraints;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use TanoConsulting\DataValidatorBundle\ConstraintValidator;

abstract class DatabaseValidator extends ConstraintValidator
{
    /** @var \Doctrine\DBAL\Schema\Table[] */
    protected static $tables;

    /**
     * @param string|Connection $dsnOrConnection string format: 'mysql://user:secret@localhost/mydb'
     * @return Connection
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getConnection($dsnOrConnection)
    {
        if (is_string($dsnOrConnection)) {
            // lazy connect to the db
            $dsnOrConnection = DriverManager::getConnection(['url' => $dsnOrConnection]);
        }

        return $dsnOrConnection;
    }

    protected function analyzeSchema($connection)
    {
        if (static::$tables === null) {
            /** @var \Doctrine\DBAL\Schema\AbstractSchemaManager $sm */
            $sm = $connection->getSchemaManager();
            foreach ($sm->listTables() as $table) {
                static::$tables[$table->getName()] = $table;
            }
        }
    }
}
