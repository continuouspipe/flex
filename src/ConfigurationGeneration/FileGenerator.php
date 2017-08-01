<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration;

use League\Flysystem\FilesystemInterface;

interface FileGenerator
{
    /**
     * Returns the generated configuration file.
     *
     * @param FilesystemInterface $filesystem
     * @param array $context
     *
     * @throws GenerationException
     *
     * @return GeneratedFile[]
     */
    public function generate(FilesystemInterface $filesystem, array $context) : array;
}
