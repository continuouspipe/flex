<?php

namespace ContinuousPipe\Flex;

use League\Flysystem\FilesystemInterface;

interface ConfigurationFileGenerator
{
    /**
     * Returns the generated configuration file.
     *
     * @param FilesystemInterface $filesystem
     * @param array $context
     *
     * @throws FlexException
     *
     * @return string
     */
    public function generate(FilesystemInterface $filesystem, array $context);
}
