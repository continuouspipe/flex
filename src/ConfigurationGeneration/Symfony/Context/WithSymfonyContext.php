<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Symfony\Context;

use ContinuousPipe\Flex\ConfigurationGeneration\ConfigurationGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedConfiguration;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Dotenv\Dotenv;

final class WithSymfonyContext implements ConfigurationGenerator
{
    /**
     * @var ConfigurationGenerator
     */
    private $decoratedGenerator;

    /**
     * @param ConfigurationGenerator $decoratedGenerator
     */
    public function __construct(ConfigurationGenerator $decoratedGenerator)
    {
        $this->decoratedGenerator = $decoratedGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $fileSystem, array $context = []) : GeneratedConfiguration
    {
        try {
            $context['env'] = (new Dotenv())->parse($fileSystem->read('.env.dist'));
        } catch (FileNotFoundException $e) {
            $context['env'] = [];
        }

        return $this->decoratedGenerator->generate($fileSystem, $context);
    }
}
