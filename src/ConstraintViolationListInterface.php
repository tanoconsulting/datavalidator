<?php

namespace TanoConsulting\DataValidatorBundle;

interface ConstraintViolationListInterface extends \Traversable, \Countable, \ArrayAccess
{
    /**
     * Adds a constraint violation to this list.
     */
    public function add(ConstraintViolationInterface $violation);

    /**
     * Merges an existing violation list into this list.
     */
    public function addAll(self $otherList);

    /**
     * Returns the violation at a given offset.
     *
     * @param int $offset The offset of the violation
     *
     * @return ConstraintViolationInterface The violation
     *
     * @throws \OutOfBoundsException if the offset does not exist
     */
    public function get(int $offset);

    /**
     * Returns whether the given offset exists.
     *
     * @param int $offset The violation offset
     *
     * @return bool Whether the offset exists
     */
    public function has(int $offset);

    /**
     * Sets a violation at a given offset.
     *
     * @param int $offset The violation offset
     */
    public function set(int $offset, ConstraintViolationInterface $violation);

    /**
     * Removes a violation at a given offset.
     *
     * @param int $offset The offset to remove
     */
    public function remove(int $offset);
}
