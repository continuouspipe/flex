<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Sequentially\Filesystem;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

/**
 * This filesystem will use other file systems as various layers. This is be especially useful
 * during the code generation to write generated files to be re-used by other generators.
 *
 */
final class LayeredFilesystem extends AbstractFilesystemImplementation
{
    /**
     * @var array|FilesystemInterface[]
     */
    private $layers;

    /**
     * @param FilesystemInterface[] $layers
     */
    public function __construct(array $layers)
    {
        if (empty($layers)) {
            throw new \InvalidArgumentException('The layered implementation expects at least one layer');
        }

        $this->layers = $layers;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        foreach ($this->layers as $layer) {
            if ($layer->has($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        foreach ($this->layers as $layer) {
            if ($layer->has($path)) {
                return $layer->read($path);
            }
        }

        throw new FileNotFoundException($path);
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, array $config = [])
    {
        $this->layers[0]->write($path, $contents, $config);
    }
}
