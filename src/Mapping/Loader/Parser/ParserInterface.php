<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser;

interface ParserInterface
{
    /**
     * @param string $data
     * @return array constraint definitions
     * @throws \Exception
     */
    public function parse($data);
}
