<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser;

class JsonParser
{
    /**
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function parse($data)
    {
        $data = json_decode($data);

        /// @todo add json_last_error() info

        if (!is_array($data)) {
            throw new \Exception("Invalid YAML config file: not an array");
        }

        return $data;
    }
}
