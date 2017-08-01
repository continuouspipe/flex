<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Sequentially;

use ContinuousPipe\Flex\ConfigurationGeneration\FileGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedFile;
use ContinuousPipe\Flex\ConfigurationGeneration\GenerationException;
use League\Flysystem\FilesystemInterface;

class IgnoreFilesThatAlreadyExists implements FileGenerator
{
    /**
     * @var FileGenerator
     */
    private $decoratedGenerator;

    /**
     * @param FileGenerator $decoratedGenerator
     */
    public function __construct(FileGenerator $decoratedGenerator)
    {
        $this->decoratedGenerator = $decoratedGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $filesystem, array $context): array
    {
        return array_filter($this->decoratedGenerator->generate($filesystem, $context), function (GeneratedFile $file) use ($filesystem) {
            return !$filesystem->has($file->getPath());
        });
    }
}
