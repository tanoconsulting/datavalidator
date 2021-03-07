<?php

namespace TanoConsulting\DataValidatorBundle;

use eZ\Publish\API\Repository\Exceptions\Exception;
use Symfony\Component\Validator\Exception\ValidatorException;
use TanoConsulting\DataValidatorBundle\Context\DatabaseExecutionContextFactory;
use TanoConsulting\DataValidatorBundle\Mapping\Factory\DatabaseMetadataFactory;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\LoaderChain;
use TanoConsulting\DataValidatorBundle\Validator\DatabaseValidator;

class DatabaseValidatorBuilder extends ValidatorBuilder
{
    /**
     * Builds and returns a new db validator object.
     *
     * @return DatabaseValidator
     */
    public function getValidator()
    {
        $metadataFactory = $this->metadataFactory;

        if (!$metadataFactory) {
            $loaders = $this->getLoaders();
            $loader = null;

            if (\count($loaders) > 1) {
                $loader = new LoaderChain($loaders);
            } elseif (1 === \count($loaders)) {
                $loader = $loaders[0];
            } else {
                throw new ValidatorException('At least one loader for configuration metadata is required');
            }

            $metadataFactory = new DatabaseMetadataFactory($loader);
        }

        $contextFactory = new DatabaseExecutionContextFactory($this->operatingMode);

        $validatorFactory = $this->validatorFactory ?: new ConstraintValidatorFactory();

        return new DatabaseValidator($contextFactory, $metadataFactory, $validatorFactory);
    }
}
