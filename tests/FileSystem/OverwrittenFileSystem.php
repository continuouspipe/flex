<?php

namespace ContinuousPipe\Flex\FileSystem;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\Handler;
use League\Flysystem\PluginInterface;

class OverwrittenFileSystem implements FilesystemInterface
{
    /**
     * @var FilesystemInterface
     */
    private $decoratedFilesystem;

    /**
     * @var array
     */
    private $overwrittenFiles = [];

    /**
     * @param FilesystemInterface $decoratedFilesystem
     */
    public function __construct(FilesystemInterface $decoratedFilesystem)
    {
        $this->decoratedFilesystem = $decoratedFilesystem;
    }

    /**
     * @param string $path
     * @param string $contents
     */
    public function setOverwrittenFile(string $path, string $contents)
    {
        $this->overwrittenFiles[$path] = $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        if (isset($this->overwrittenFiles[$path])) {
            return true;
        }

        return $this->decoratedFilesystem->has($path);
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        if (isset($this->overwrittenFiles[$path])) {
            return $this->overwrittenFiles[$path];
        }

        return $this->decoratedFilesystem->read($path);
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        return $this->decoratedFilesystem->readStream($path);
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->decoratedFilesystem->listContents($directory, $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        return $this->decoratedFilesystem->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        return $this->decoratedFilesystem->getSize($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        return $this->decoratedFilesystem->getMimetype($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        return $this->decoratedFilesystem->getTimestamp($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility($path)
    {
        return $this->decoratedFilesystem->getVisibility($path);
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, array $config = [])
    {
        return $this->decoratedFilesystem->write($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, array $config = [])
    {
        return $this->decoratedFilesystem->writeStream($path, $resource, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, array $config = [])
    {
        return $this->decoratedFilesystem->update($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, array $config = [])
    {
        return $this->decoratedFilesystem->updateStream($path, $resource, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        return $this->decoratedFilesystem->rename($path, $newpath);
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        return $this->decoratedFilesystem->copy($path, $newpath);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        return $this->decoratedFilesystem->delete($path);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        return $this->decoratedFilesystem->deleteDir($dirname);
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, array $config = [])
    {
        return $this->decoratedFilesystem->createDir($dirname, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($path, $visibility)
    {
        return $this->decoratedFilesystem->setVisibility($path, $visibility);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $contents, array $config = [])
    {
        return $this->decoratedFilesystem->put($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function putStream($path, $resource, array $config = [])
    {
        return $this->decoratedFilesystem->putStream($path, $resource, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function readAndDelete($path)
    {
        return $this->decoratedFilesystem->readAndDelete($path);
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, Handler $handler = null)
    {
        return $this->decoratedFilesystem->get($path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function addPlugin(PluginInterface $plugin)
    {
        return $this->decoratedFilesystem->addPlugin($plugin);
    }
}
