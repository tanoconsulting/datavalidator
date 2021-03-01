<?php

namespace TanoConsulting\DataValidatorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TanoConsultingDataValidatorBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
