<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Sequentially\Filesystem;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\Handler;
use League\Flysystem\PluginInterface;

abstract class AbstractFilesystemImplementation implements FilesystemInterface
{
    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, array $config = [])
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, array $config = [])
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, array $config = [])
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, array $config = [])
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, array $config = [])
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($path, $visibility)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $contents, array $config = [])
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function putStream($path, $resource, array $config = [])
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function readAndDelete($path)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, Handler $handler = null)
    {
        throw new \RuntimeException('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function addPlugin(PluginInterface $plugin)
    {
        throw new \RuntimeException('Not supported');
    }
}
