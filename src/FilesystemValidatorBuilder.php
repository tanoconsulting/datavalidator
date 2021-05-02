<?php

namespace TanoConsulting\DataValidatorBundle;

use Symfony\Component\Validator\Exception\ValidatorException;
use TanoConsulting\DataValidatorBundle\Context\FilesystemExecutionContextFactory;
use TanoConsulting\DataValidatorBundle\Mapping\Factory\FilesystemMetadataFactory;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Filesystem\FileLoader;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\LoaderChain;
use TanoConsulting\DataValidatorBundle\Validator\FilesystemValidator;

class FilesystemValidatorBuilder extends ValidatorBuilder
{
    /**
     * Builds and returns a new db validator object.
     *
     * @return FilesystemValidator
     */
    public function getValidator()
    {
        $contextFactory = $this->executionContextFactory ?:new FilesystemExecutionContextFactory($this->operatingMode);

        $metadataFactory = $this->metadataFactory;
        if (!$metadataFactory) {
            $loaders = $this->getLoaders();

            if (\count($loaders) > 1) {
                $loader = new LoaderChain($loaders);
            } elseif (1 === \count($loaders)) {
                $loader = $loaders[0];
            } else {
                throw new ValidatorException('At least one loader for configuration metadata is required');
            }

            $metadataFactory = new FilesystemMetadataFactory($loader);
        }

        $validatorFactory = $this->validatorFactory ?: new ConstraintValidatorFactory();

        $eventDispatcher = $this->eventDispatcher;

        return new FilesystemValidator($contextFactory, $metadataFactory, $validatorFactory, $eventDispatcher);
    }

    public function getLoaders()
    {
        $loaders = [];

        foreach ($this->fileMappings as $fileMapping) {
            $loaders[] = new FileLoader($fileMapping);
        }

        return array_merge($loaders, $this->loaders);
    }
}
