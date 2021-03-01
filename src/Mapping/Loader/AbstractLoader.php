<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader;

use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\Exception\MappingException;

abstract class AbstractLoader
{
    /**
     * The namespace to load constraints from by default.
     */
    static $DEFAULT_NAMESPACE = '\\TanoConsulting\\DataValidatorBundle\\Constraints\\';

    protected $namespaces = [];

    /**
     * Adds a namespace alias.
     *
     * The namespace alias can be used to reference constraints from specific
     * namespaces in {@link newConstraint()}:
     *
     *     $this->addNamespaceAlias('mynamespace', '\\Acme\\Package\\Constraints\\');
     *
     *     $constraint = $this->newConstraint('mynamespace:NotNull');
     */
    protected function addNamespaceAlias(string $alias, string $namespace)
    {
        $this->namespaces[$alias] = $namespace;
    }

    /**
     * Creates a new constraint instance for the given constraint name.
     *
     * @param string $name    The constraint name. Either a constraint relative
     *                        to the default constraint namespace, or a fully
     *                        qualified class name. Alternatively, the constraint
     *                        may be preceded by a namespace alias and a colon.
     *                        The namespace alias must have been defined using
     *                        {@link addNamespaceAlias()}.
     * @param mixed  $options The constraint options
     *
     * @return Constraint
     *
     * @throws MappingException If the namespace prefix is undefined
     */
    protected function newConstraint(string $name, $options = null)
    {
        if (false !== strpos($name, '\\') && class_exists($name)) {
            $className = (string) $name;
        } elseif (false !== strpos($name, ':')) {
            list($prefix, $className) = explode(':', $name, 2);

            if (!isset($this->namespaces[$prefix])) {
                throw new MappingException(sprintf('Undefined namespace prefix "%s".', $prefix));
            }

            $className = $this->namespaces[$prefix].$className;
        } else {
            $className = static::$DEFAULT_NAMESPACE.$name;
        }

        return new $className($options);
    }
}
