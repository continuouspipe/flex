<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration;

use League\Flysystem\FilesystemInterface;

interface ConfigurationGenerator
{
    /**
     * Returns the generated configuration files.
     *
     * @param FilesystemInterface $fileSystem
     * @param array $context
     *
     * @throws GenerationException
     *
     * @return GeneratedConfiguration
     */
    public function generate(FilesystemInterface $fileSystem, array $context = []) : GeneratedConfiguration;
}
