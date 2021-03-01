<?php


namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser;

use Symfony\Component\Yaml\Parser;

class YamlParser
{
    /**
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function parse($data)
    {
        $parser = new Parser();
        $data = $parser->parse($data);

        if (!is_array($data)) {
            throw new \Exception("Invalid YAML config file: not an array");
        }

        return $data;
    }
}
