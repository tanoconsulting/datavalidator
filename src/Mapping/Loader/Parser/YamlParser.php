<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser;

use Symfony\Component\Yaml\Parser;
use TanoConsulting\DataValidatorBundle\Exception\MappingException;

class YamlParser implements ParserInterface
{
    /**
     * @see self::loadMetadata for the expected format
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function parse($data)
    {
        $parser = new Parser();
        $data = $parser->parse($data);

        if (!is_array($data)) {
            throw new MappingException('Invalid YAML config file: not an array');
        }

        return $data;
    }
}
