<?php

namespace TanoConsulting\DataValidatorBundle;

/**
 * Contains the properties of a constraint definition.
 */
abstract class Constraint
{
    /**
     * Initializes the constraint with options.
     *
     * You should pass an associative array. The keys should be the names of
     * existing properties in this class. The values should be the value for these
     * properties.
     *
     * Alternatively you can override the method getDefaultOption() to return the
     * name of an existing property. If no associative array is passed, this
     * property is set instead.
     *
     * You can force that certain options are set by overriding
     * getRequiredOptions() to return the names of these options. If any
     * option is not set here, an exception is thrown.
     *
     * @param mixed    $options The options (as associative array)
     *                          or the value for the default
     *                          option (any other type)
     * @param string[] $groups  An array of validation groups
     * @param mixed    $payload Domain-specific data attached to a constraint
     *
     * @throws InvalidOptionsException       When you pass the names of non-existing
     *                                       options
     * @throws MissingOptionsException       When you don't pass any of the options
     *                                       returned by getRequiredOptions()
     * @throws ConstraintDefinitionException When you don't pass an associative
     *                                       array, but getDefaultOption() returns
     *                                       null
     *
     * @todo Q: is it worth to go all the trouble with this dynamic constructor ?
     */
    /*public function __construct($options = null /*, array $groups = null, $payload = null* /)
    {
        $options = $this->normalizeOptions($options);
        //if (null !== $groups) {
        //    $options['groups'] = $groups;
        //}
        //$options['payload'] = $payload ?? $options['payload'] ?? null;

        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }*/

    /*protected function normalizeOptions($options): array
    {
        $normalizedOptions = [];
        $defaultOption = $this->getDefaultOption();
        $invalidOptions = [];
        $missingOptions = array_flip((array) $this->getRequiredOptions());
        $knownOptions = get_class_vars(static::class);

        // The "groups" option is added to the object lazily
        //$knownOptions['groups'] = true;

        //if (\is_array($options) && isset($options['value']) && !property_exists($this, 'value')) {
        //    if (null === $defaultOption) {
        //        throw new ConstraintDefinitionException(sprintf('No default option is configured for constraint "%s".', static::class));
        //    }
        //
        //    $options[$defaultOption] = $options['value'];
        //    unset($options['value']);
        //}

        if (\is_array($options)) {
            reset($options);
        }
        if ($options && \is_array($options) && \is_string(key($options))) {
            foreach ($options as $option => $value) {
                if (\array_key_exists($option, $knownOptions)) {
                    $normalizedOptions[$option] = $value;
                    unset($missingOptions[$option]);
                } else {
                    $invalidOptions[] = $option;
                }
            }
        } elseif (null !== $options && !(\is_array($options) && 0 === \count($options))) {
            if (null === $defaultOption) {
                throw new ConstraintDefinitionException(sprintf('No default option is configured for constraint "%s".', static::class));
            }

            if (\array_key_exists($defaultOption, $knownOptions)) {
                $normalizedOptions[$defaultOption] = $options;
                unset($missingOptions[$defaultOption]);
            } else {
                $invalidOptions[] = $defaultOption;
            }
        }

        if (\count($invalidOptions) > 0) {
            throw new InvalidOptionsException(sprintf('The options "%s" do not exist in constraint "%s".', implode('", "', $invalidOptions), static::class), $invalidOptions);
        }

        if (\count($missingOptions) > 0) {
            throw new MissingOptionsException(sprintf('The options "%s" must be set for constraint "%s".', implode('", "', array_keys($missingOptions)), static::class), array_keys($missingOptions));
        }

        return $normalizedOptions;
    }*/

    /**
     * Returns the name of the default option.
     *
     * Override this method to define a default option.
     *
     * @return string|null
     *
     * @see __construct()
     */
    /*public function getDefaultOption()
    {
        return null;
    }*/

    /**
     * Returns the name of the required options.
     *
     * Override this method if you want to define required options.
     *
     * @return array
     *
     * @see __construct()
     */
    /*public function getRequiredOptions()
    {
        return [];
    }*/

    /**
     * Returns the name of the class that validates this constraint.
     *
     * By default, this is the fully qualified name of the constraint class
     * suffixed with "Validator". You can override this method to change that
     * behavior.
     *
     * @return string
     */
    public function validatedBy()
    {
        return static::class.'Validator';
    }

    /**
     * Returns whether the constraint can be put onto schemas, table columns, etc....
     *
     * @return string|string[] One or more constant values
     */
    //abstract public function getTargets();

    /**
     * NB: this is not in the upstream API
     * @return string
     */
    abstract public function getName();
}
