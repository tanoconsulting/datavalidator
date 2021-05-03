<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Database;

class TaggedServiceLoader extends LoaderBag
{
    /// @todo should we make this lazy, and only get the constraints on access ?
    public function addConstraintDefinitionProvider($provideService)
    {
        /// @todo throw with meaningful exception if getConstraintDefinitions is not callable
        $this->addConstraintDefinitions($provideService->getConstraintDefinitions());
    }
}
