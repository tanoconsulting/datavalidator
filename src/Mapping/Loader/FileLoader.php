<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader;

use TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser\JsonParser;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser\YamlParser;
use TanoConsulting\DataValidatorBundle\Mapping\Metadata;

abstract class FileLoader implements LoaderInterface
{
    protected $file;

    abstract protected function createConstraint($constraintDefinition);

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
            throw new MappingException(sprintf('The mapping file "%s" does not exist.', $file));
        }

        if (!is_readable($file)) {
            throw new MappingException(sprintf('The mapping file "%s" is not readable.', $file));
        }

        /*if (!stream_is_local($this->file)) {
            throw new MappingException(sprintf('The mapping file "%s" is not a local file.', $file));
        }*/

        if (!$this->canParse($file)) {
            throw new \Exception('Unsupported file format for config file: ' . $file);
        }

        $this->file = $file;
    }

    public function loadMetadata(Metadata $metadata)
    {
        foreach($this->parse($this->file) as $constraintDefinition)
        {
            $metadata->addConstraint($this->createConstraint($constraintDefinition));
        }
    }

    protected function canParse($file)
    {
        return preg_match('/\.(json|yaml|yml)$/', $file);
    }

    protected function parse($file)
    {
        switch(pathinfo($file, PATHINFO_EXTENSION))
        {
            //case 'ini':
            case 'json':
                $parser = new JsonParser();
                break;
            case 'yaml':
            case 'yml':
                $parser = new YamlParser();
                break;
            default:
                throw new \Exception('');
        }
        return $parser->parse(file_get_contents($file));
    }
}
