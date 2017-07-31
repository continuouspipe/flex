<?php

namespace ContinuousPipe\Flex;

use League\Flysystem\FilesystemInterface;

final class GenerateFilesAsPerGeneratorMapping implements ConfigurationFileCollectionGenerator
{
    /**
     * @var array<string,ConfigurationFileGenerator>
     */
    private $fileGeneratorMapping;

    /**
     * @param array<string,ConfigurationFileGenerator> $fileGeneratorMapping
     */
    public function __construct(array $fileGeneratorMapping)
    {
        $this->fileGeneratorMapping = $fileGeneratorMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $fileSystem, array $configuration)
    {
        return array_map(function (ConfigurationFileGenerator $generator) use ($fileSystem, $configuration) {
            return $generator->generate($fileSystem, $configuration);
        }, $this->fileGeneratorMapping);
    }

    /**
     * {@inheritdoc}
     */
    public function checkAvailability(FilesystemInterface $fileSystem, array $configuration)
    {
        return null;
    }
}
