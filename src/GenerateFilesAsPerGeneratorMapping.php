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
    public function generate(FilesystemInterface $fileSystem, array $context = [])
    {
        return array_map(function (ConfigurationFileGenerator $generator) use ($fileSystem, $context) {
            return $generator->generate($fileSystem, $context);
        }, $this->fileGeneratorMapping);
    }

    /**
     * {@inheritdoc}
     */
    public function checkAvailability(FilesystemInterface $fileSystem, array $context = [])
    {
        return null;
    }
}
