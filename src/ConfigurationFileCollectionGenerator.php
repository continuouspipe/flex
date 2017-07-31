<?php

namespace ContinuousPipe\Flex;

use League\Flysystem\FilesystemInterface;

interface ConfigurationFileCollectionGenerator
{
    /**
     * Returns the generated configuration files.
     *
     * @param FilesystemInterface $fileSystem
     * @param array $context
     *
     * @throws FlexException
     *
     * @return array<string,string>
     */
    public function generate(FilesystemInterface $fileSystem, array $context = []);

    /**
     * Returns true if the generator supports such type of application.
     *
     * @param FilesystemInterface $fileSystem
     * @param array $context
     *
     * @return AvailabilityException|null
     */
    public function checkAvailability(FilesystemInterface $fileSystem, array $context = []);
}
