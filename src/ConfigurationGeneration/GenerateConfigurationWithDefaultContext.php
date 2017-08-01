<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration;

use ContinuousPipe\Flex\ConfigurationGeneration\ConfigurationGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedConfiguration;
use ContinuousPipe\Flex\ConfigurationGeneration\GenerationException;
use League\Flysystem\FilesystemInterface;

final class GenerateConfigurationWithDefaultContext implements ConfigurationGenerator
{
    /**
     * @var ConfigurationGenerator
     */
    private $decoratedGenerator;

    /**
     * @var array
     */
    private $defaultContext;

    /**
     * @param ConfigurationGenerator $decoratedGenerator
     * @param array $defaultContext
     */
    public function __construct(ConfigurationGenerator $decoratedGenerator, array $defaultContext)
    {
        $this->decoratedGenerator = $decoratedGenerator;
        $this->defaultContext = $defaultContext;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $fileSystem, array $context = []): GeneratedConfiguration
    {
        return $this->decoratedGenerator->generate($fileSystem, array_merge($this->defaultContext, $context));
    }
}
