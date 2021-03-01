<?php

namespace TanoConsulting\DataValidatorBundle\Context;

use Symfony\Contracts\Translation\TranslatorInterface;
use TanoConsulting\DataValidatorBundle\ConstraintViolationInterface;
use TanoConsulting\DataValidatorBundle\ConstraintViolationList;

abstract class ExecutionContext implements ExecutionContextInterface
{
    /**
     * @var TranslatorInterface
     */
    //protected $translator;

    /**
     * @var string
     */
    //protected $translationDomain;

    protected $violations;

    protected $operatingMode = self::MODE_COUNT;

    protected function __construct()
    {
        $this->violations = new ConstraintViolationList();
    }

    //protected function setTranslator(TranslatorInterface $translator, string $translationDomain = null)
    //{
    //    $this->translator = $translator;
    //    $this->translationDomain = $translationDomain;
    //}

    public function addViolation(ConstraintViolationInterface $violation)
    {
        $this->violations->add($violation);
    }

    public function getViolations()
    {
        return $this->violations;
    }

    public function getOperatingMode()
    {
        return $this->operatingMode;
    }
}
