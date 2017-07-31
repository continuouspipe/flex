<?php

namespace ContinuousPipe\Flex\Symfony;

use ContinuousPipe\Flex\AvailabilityException;
use ContinuousPipe\Flex\ConfigurationFileCollectionGenerator;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Dotenv\Dotenv;

final class GenerateFilesWithSymfonyContext implements ConfigurationFileCollectionGenerator
{
    /**
     * @var ConfigurationFileCollectionGenerator
     */
    private $decoratedGenerator;

    /**
     * @param ConfigurationFileCollectionGenerator $decoratedGenerator
     */
    public function __construct(ConfigurationFileCollectionGenerator $decoratedGenerator)
    {
        $this->decoratedGenerator = $decoratedGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $fileSystem, array $configuration)
    {
        try {
            $configuration['env'] = (new Dotenv())->parse($fileSystem->read('.env.dist'));
        } catch (FileNotFoundException $e) {
            throw new AvailabilityException('File `.env.dist` do not exists in your repository');
        }

        return $this->decoratedGenerator->generate($fileSystem, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function checkAvailability(FilesystemInterface $fileSystem, array $configuration)
    {
        try {
            $composerFile = $fileSystem->read('composer.json');
        } catch (FileNotFoundException $e) {
            return new AvailabilityException('File `composer.json` not found in the repository', $e->getCode(), $e);
        }

        try {
            $composer = \GuzzleHttp\json_decode($composerFile, true);
        } catch (\InvalidArgumentException $e) {
            return new AvailabilityException('File `composer.json` is not a valid JSON file');
        }

        if (!isset($composer['require']) || !isset($composer['require']['symfony/flex'])) {
            return new AvailabilityException('`symfony/flex` is not a dependency of your project');
        }

        if (!$fileSystem->has('.env.dist')) {
            return new AvailabilityException('File `.env.dist` do not exists in your repository');
        }

        return null;
    }
}
