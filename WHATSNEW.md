Version 1.0-alpha2
==================

- Fixed: in dry-run mode, use the correct name for foreign-key constraints

- New: allow interrupting gracefully the validation command

- Improved: Improve output of the validation command: in verbose mode, print the number of constraints found, the name
  of each constraint before validating it and at the end the time taken and max memory used

- Code refactoring: use a common base for all exceptions of this bundle; make API more similar to the Symfony Validator
  Component one


Version 1.0-alpha
=================

Initial release
