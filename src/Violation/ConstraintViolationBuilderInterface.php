<?php

namespace TanoConsulting\DataValidatorBundle\Violation;

/// @todo finish or remove ...
interface ConstraintViolationBuilderInterface
{
    /**
     * Sets a parameter to be inserted into the violation message.
     *
     * @param string $key   The name of the parameter
     * @param string $value The value to be inserted in the parameter's place
     *
     * @return $this
     */
    public function setParameter(string $key, string $value);

    /**
     * Sets all parameters to be inserted into the violation message.
     *
     * @param array $parameters An array with the parameter names as keys and
     *                          the values to be inserted in their place as
     *                          values
     *
     * @return $this
     */
    public function setParameters(array $parameters);

    /**
     * Sets the translation domain which should be used for translating the
     * violation message.
     *
     * @param string $translationDomain The translation domain
     *
     * @return $this
     *
     * @see \Symfony\Contracts\Translation\TranslatorInterface
     */
    public function setTranslationDomain(string $translationDomain);

    /**
     * Adds the violation to the current execution context.
     */
    public function addViolation();
}
