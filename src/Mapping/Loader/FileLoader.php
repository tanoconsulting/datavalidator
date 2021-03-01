<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader;

use TanoConsulting\DataValidatorBundle\Exception\MappingException;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser;
use TanoConsulting\DataValidatorBundle\Mapping\Metadata;

abstract class FileLoader extends AbstractLoader implements LoaderInterface
{
    protected $file;

    /**
     * Creates a new loader.
     *
     * @param string $file The mapping file to load
     *
     * @throws MappingException If the file does not exist or is not readable
     */
    public function __construct(string $file)
    {
        if (!is_file($file)) {
            throw new MappingException(sprintf('The config file "%s" does not exist.', $file));
        }

        if (!is_readable($file)) {
            throw new MappingException(sprintf('The config file "%s" is not readable.', $file));
        }

        if (!stream_is_local($this->file)) {
            throw new MappingException(sprintf('The config file "%s" is not a local file.', $file));
        }

        if (!$this->canParse($file)) {
            throw new MappingException('Unsupported file format for config file: ' . $file);
        }

        $this->file = $file;
    }

    /**
     * We strive to keep a similar format to Symfony Validator.
     * @param Metadata $metadata
     * @throws MappingException
     */
    public function loadMetadata(Metadata $metadata)
    {
        $data = $this->parse($this->file);

        if (isset($data['namespaces'])) {
            foreach ($data['namespaces'] as $alias => $namespace) {
                $this->addNamespaceAlias($alias, $namespace);
            }
        }

        if (isset($data['constraints'])) {
            foreach ($data['constraints'] as $constraintDefinition) {
                if (!is_array($constraintDefinition) || count($constraintDefinition) !== 1) {
                    throw new MappingException('Invalid config file syntax');
                }
                $class = key($constraintDefinition);
                $options = current($constraintDefinition);
                $metadata->addConstraint($this->newConstraint($class, $options));
            }
        }

        /// @todo throw if there are no constraints defined ?
    }

    protected function canParse($file)
    {
        return preg_match('/\.(json|yaml|yml|ini)$/', $file);
    }

    protected function parse($file)
    {
        switch(pathinfo($file, PATHINFO_EXTENSION))
        {
            case 'ini':
                $parser = new Parser\EzIniParser();
                break;
            case 'json':
                $parser = new Parser\JsonParser();
                break;
            case 'yaml':
            case 'yml':
                $parser = new Parser\YamlParser();
                break;
            default:
                throw new MappingException('Unsupported file format for config file: ' . $file);
        }
        return $parser->parse(file_get_contents($file));
    }
}
