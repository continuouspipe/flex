<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Sequentially\Filesystem;

use League\Flysystem\Config;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

class InMemoryFileSystem extends AbstractFilesystemImplementation
{
    private $memory = [];

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return array_key_exists($path, $this->memory);
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException($path);
        }

        return $this->memory[$path];
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, array $config = [])
    {
        $this->memory[$path] = $contents;

        return true;
    }
}
