<?php

namespace ContinuousPipe\Flex;

use League\Flysystem\FilesystemInterface;

final class GenerateFilesWithDefaultContext implements ConfigurationFileCollectionGenerator
{
    /**
     * @var ConfigurationFileCollectionGenerator
     */
    private $decoratedGenerator;

    /**
     * @var array
     */
    private $defaultContext;

    /**
     * @param ConfigurationFileCollectionGenerator $decoratedGenerator
     * @param array $defaultContext
     */
    public function __construct(ConfigurationFileCollectionGenerator $decoratedGenerator, array $defaultContext)
    {
        $this->decoratedGenerator = $decoratedGenerator;
        $this->defaultContext = $defaultContext;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $fileSystem, array $context = [])
    {
        return $this->decoratedGenerator->generate($fileSystem, array_merge($this->defaultContext, $context));
    }

    /**
     * {@inheritdoc}
     */
    public function checkAvailability(FilesystemInterface $fileSystem, array $context = [])
    {
        return $this->decoratedGenerator->checkAvailability($fileSystem, array_merge($this->defaultContext, $context));
    }
}
