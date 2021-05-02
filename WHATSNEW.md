Version 1.0-beta1 (unreleased)
==============================

- New: the Validator used to check each type of Constraint can now be a Symfony Service instead of a plain php class.
  The logic has been changed so that the name of the Validator obtained by the constraint is first checked against
  existing services in the Container. If a service is found it is used. If not, the name is asssumed to be a class
  name, and an instance of that class is created on the fly

- New: it is now possible to declare a set required tables for SQL Query constraints. If any of those tables is missing
  from the database, the constraint check will be skipped

- New: its is now possible to run filesystem validation checks, by using the command `datavalidator:validate:filesystem`.
  For the moment, the only available constraint is a regular expression to apply on file/folder names

- Fixed: the numeric suffix in the constraint names used for foreign keys now starts correctly at 1


Version 1.0-alpha2
==================

- Fixed: in dry-run mode, use the correct name for foreign-key constraints

- Fixed: use appropriate exceptions when invalid constraint definitions are found

- New: allow defining constraints via a symfony config parameter or tagged services instead of a yaml file

- New: allow interrupting gracefully the validation command (using CTRL-C / sigterm or sigint)

- Improved: Improve output of the validation command: in verbose mode, print the number of constraints found, the name
  of each constraint before validating it and at the end the time taken and max memory used

- Code refactoring: use a common base for all exceptions of this bundle; make API more similar to the Symfony Validator
  Component one


Version 1.0-alpha
=================

Initial release
