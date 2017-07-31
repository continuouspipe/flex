<?php

namespace ContinuousPipe\Flex;

use League\Flysystem\FilesystemInterface;

interface ConfigurationFileCollectionGenerator
{
    /**
     * Returns the generated configuration files.
     *
     * @param FilesystemInterface $fileSystem
     * @param array $configuration
     *
     * @throws FlexException
     *
     * @return array<string,string>
     */
    public function generate(FilesystemInterface $fileSystem, array $configuration);

    /**
     * Returns true if the generator supports such type of application.
     *
     * @param FilesystemInterface $fileSystem
     * @param array $configuration
     *
     * @return AvailabilityException|null
     */
    public function checkAvailability(FilesystemInterface $fileSystem, array $configuration);
}
