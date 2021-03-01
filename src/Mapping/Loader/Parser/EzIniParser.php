<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Parser;

class EzIniParser implements ParserInterface
{

    /**
     * Parses ini files from legacy ezdbintegrity extension
     *
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function parse($data)
    {
        $data = parse_ini_string(preg_replace('/^#/m', ';', $data), true, INI_SCANNER_RAW);

        if (!is_array($data)) {
            throw new \Exception("Invalid Ini config file: not an array");
        }

        $out = [];

        if (isset($data['ForeignKeys']) && is_array($data['ForeignKeys']))
        {
            foreach($data['ForeignKeys'] as $name => $value) {
                foreach ($value as $target) {
                    $pieces = explode('::', $target);
                    $out[] = [
                        'ForeignKey' => [
                            'child' => [
                                $name => explode(',', $pieces[0])
                            ],
                            'parent' => [
                                $pieces[1] => explode(',', $pieces[2])
                            ],
                            'except' => (isset($pieces[3]) ? $pieces[3] : null)
                        ]
                    ];
                }
            }
        }

        if (isset($data['CustomQueries']) && is_array($data['CustomQueries']))
        {
            foreach($data['CustomQueries'] as $name => $value) {
                $out[] = ['Query' => ['name' => $name, 'sql' => $value['sql']]];
            }
        }

        return ['constraints' => $out];
    }
}
