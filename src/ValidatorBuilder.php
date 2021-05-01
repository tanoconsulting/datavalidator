<?php

namespace TanoConsulting\DataValidatorBundle;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use TanoConsulting\DataValidatorBundle\Context\DatabaseExecutionContext;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextFactoryInterface;
use TanoConsulting\DataValidatorBundle\Mapping\Factory\MetadataFactoryInterface;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\Database\FileLoader;
use TanoConsulting\DataValidatorBundle\Mapping\Loader\LoaderInterface;

/**
 * @todo add an interface
 */
abstract class ValidatorBuilder
{
    /**
     * @var ConstraintValidatorFactoryInterface|null
     */
    protected $validatorFactory;

    protected $loaders = [];

    /**
     * @var MetadataFactoryInterface|null
     */
    protected $metadataFactory;

    protected $fileMappings = [];

    /** @var ExecutionContextFactoryInterface|null */
    protected $executionContextFactory;

    protected $operatingMode = DatabaseExecutionContext::MODE_COUNT;

    protected $eventDispatcher;

    /**
     * Sets the constraint validator factory used by the validator.
     *
     * @return $this
     */
    public function setConstraintValidatorFactory(ConstraintValidatorFactoryInterface $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;

        return $this;
    }

    /**
     * Sets the class metadata factory used by the validator.
     *
     * @return $this
     */
    public function setMetadataFactory(MetadataFactoryInterface $metadataFactory)
    {
        if (\count($this->getLoaders()) > 0) {
            throw new ValidatorException('You cannot set a custom metadata factory after adding custom mappings. You should do either of both.');
        }

        $this->metadataFactory = $metadataFactory;

        return $this;
    }

    /**
     * Adds a constraint mapping file to the validator.
     *
     * @param string $path The path to the mapping file
     *
     * @return $this
     */
    public function addFileMapping($path)
    {
        if (null !== $this->metadataFactory) {
            throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
        }

        $this->fileMappings[] = $path;

        return $this;
    }

    /**
     * Adds a list of constraint mappings files to the validator.
     *
     * @param string[] $paths The paths to the mapping files
     *
     * @return $this
     */
    public function addFileMappings(array $paths)
    {
        if (null !== $this->metadataFactory) {
            throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
        }

        $this->fileMappings = array_merge($this->fileMappings, $paths);

        return $this;
    }

    /**
     * Adds directly a custom metadata loader to the validator.
     *
     * @return $this
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;

        return $this;
    }

    public function getLoaders()
    {
        $loaders = [];

        foreach ($this->fileMappings as $fileMapping) {
            $loaders[] = new FileLoader($fileMapping);
        }

        return array_merge($loaders, $this->loaders);
    }

    public function setOperatingMode($mode)
    {
        if (null !== $this->executionContextFactory) {
            throw new ValidatorException('You cannot set a custom operating mode after setting a custom context factory. Configure your context factory instead.');
        }

        $this->operatingMode = $mode;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
