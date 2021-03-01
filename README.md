DB Data Validator Bundle
========================

*NB: Work In Progress*

Goals:
------

Allow checking integrity of data in a database, going beyond what the database schema definition enforces.

Allow checking the integrity of a set of files.

Usecases:
---------

There are many scenarios in which usage of the constraints configured in a database schema is not sufficient to
enforce data integrity, such as f.e.:

- the db engine in use not supporting advanced/complex data validation constraints
- the db engine in use does support advanced data validation constraints, but those are not being used
- data integrity constraints which are too complex to express easily using the db engine
- db native constraints having been disabled for speed during mass import operations
- constraints having been implemented in application code, with multiple apps writing to the database

In all those cases, a separate tool which can validate that the data stored in the database adheres to a set of
rules can come in handy.

Usage:
------

1. define the set of constraints in a yaml or json file, eg:

    ```yaml
    (to be defined...)
    ```

2. run the validation command

        php bin/console datavalidator:validate:database --database ... --schema-file ...

Constraints currently supported:
--------------------------------

- foreign key definitions (WIP)
- custom sql queries

Thanks
------

Code based on the Symfony/Validator component; thanks to all its developers!
