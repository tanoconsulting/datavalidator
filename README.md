DB Data Validator Bundle
========================

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

Requirements:
-------------

- php 7.3 or later
- a database supported by Doctrine DBAL (2.11 or 3.0 or later)
- Symfony components: see `composer.json`

Usage:
------

1. the set of constraints can be defined in a yaml or json file. This sample shows the supported syntax:

    ```yaml
    constraints:
      -
        ForeignKey:
          child:
            ezapprove_items: collaboration_id
          parent:
            ezcollab_item: id
      -
        ForeignKey:
          child:
            ezbinaryfile: [contentobject_attribute_id, version]
          parent:
            ezcontentobject_attribute: [id, version]
       -
        ForeignKey:
          child:
            ezcontentobject: id
          parent:
            ezcontentobject_version: contentobject_id
          except: 'ezcontentobject.status = 1 AND ezcontentobject_version.status = 1'
      -
        Query:
          name: classes_with_same_identifier
          sql: 'SELECT identifier, COUNT(*) AS identical_identifiers FROM ezcontentclass WHERE version = 0 GROUP BY identifier HAVING COUNT(*) > 1'
    ```

2. run the validation command

        php bin/console datavalidator:validate:database --schema-file=<my_schema_constraints.yaml>

    This presumes that your application has set up a database connection configuration doctrine named `default`.
    If that is not the case, you can run:

        php bin/console datavalidator:validate:database --schema-file=<my_schema_constraints.yaml> --database=<mysql://user:pwd@localhost/mydb>

    If you want to list the validations constraints without validating them run:

        php bin/console datavalidator:validate:database --schema-file=<my_schema_constraints.yaml> --dry-run

    By default the results show the number of database rows found which violate each constraint. To see the data of
    those rows instead, use:

        php bin/console datavalidator:validate:database --schema-file=<my_schema_constraints.yaml> --display-data

3. defining validation constraints in your code

    Instead of using a dedicated configuration file on the command line, you can configure the validation constraints in
    code, either:

    - by setting a value to configuration parameter `data_validator.constraints.database`, or
    - by tagging services with the `data_validator.constraint_provider.database` tag. Those services will have to
      implement a public method `getConstraintDefinitions()` that returns all the relevant constraints definitions

Constraints currently supported:
--------------------------------

- foreign key definitions (WIP)
- custom sql queries

See the doc/samples folder for examples constraints of well-known applications' database schemas.

Troubleshooting
---------------

- use the `-v` command line option to see details of execution
- if the execution of the constraint validation is taking a long time, you can use CTRL-C to stop execution halfway:
  the script will exit gracefully printing any violation found up to that point

Thanks
------

Code based on the Symfony/Validator component; thanks to all its developers!
