<?php

namespace TanoConsulting\DataValidatorBundle\Mapping\Loader\Filesystem;

class TaggedServiceLoader extends LoaderBag
{
    public function addConstraintDefinitionProvider($provideService)
    {
        /// @todo throw with meaningful exception if getConstraintDefinitions is not callable
        $this->addConstraintDefinitions($provideService->getConstraintDefinitions());
    }
}
