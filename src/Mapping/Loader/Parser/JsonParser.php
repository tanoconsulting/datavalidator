<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser;

class JsonParser implements ParserInterface
{
    /**
     * @see self::loadMetadata for the expected format
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function parse($data)
    {
        $data = json_decode($data);

        /// @todo add json_last_error() info

        if (!is_array($data)) {
            throw new \Exception("Invalid JSON config file: not an array");
        }

        return $data;
    }
}
