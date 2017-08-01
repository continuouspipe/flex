<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Sequentially;

use ContinuousPipe\Flex\ConfigurationGeneration\ConfigurationGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\FileGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedConfiguration;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedFile;
use ContinuousPipe\Flex\ConfigurationGeneration\GenerationException;
use ContinuousPipe\Flex\ConfigurationGeneration\Sequentially\Filesystem\InMemoryFileSystem;
use ContinuousPipe\Flex\ConfigurationGeneration\Sequentially\Filesystem\LayeredFilesystem;
use League\Flysystem\FilesystemInterface;

class SequentiallyGenerateFiles implements ConfigurationGenerator
{
    /**
     * @var array|FileGenerator[]
     */
    private $fileGenerators;

    /**
     * @param FileGenerator[] $fileGenerators
     */
    public function __construct(array $fileGenerators)
    {
        $this->fileGenerators = $fileGenerators;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $fileSystem, array $context = []): GeneratedConfiguration
    {
        $layeredFilesystem = new LayeredFilesystem([
            new InMemoryFileSystem(),
            $fileSystem
        ]);

        $allGeneratedFiles = [];
        foreach ($this->fileGenerators as $generator) {
            $generatedFiles = (new IgnoreFilesThatAlreadyExists($generator))->generate($layeredFilesystem, $context);

            foreach ($generatedFiles as $generatedFile) {
                /** @var GeneratedFile $generatedFile */
                $allGeneratedFiles[] = $generatedFile;

                if ($generatedFile->hasFailed()) {
                    break 2;
                }

                $layeredFilesystem->write($generatedFile->getPath(), $generatedFile->getContents());
            }
        }

        return new GeneratedConfiguration($allGeneratedFiles);
    }
}
