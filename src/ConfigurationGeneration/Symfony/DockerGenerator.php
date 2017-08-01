<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Symfony;

use ContinuousPipe\Flex\ConfigurationFileGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\FileGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedFile;
use ContinuousPipe\Flex\ConfigurationGeneration\GenerationException;
use ContinuousPipe\Flex\ConfigurationGeneration\GenerationNotification;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

final class DockerGenerator implements FileGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $filesystem, array $context) : array
    {
        try {
            $composerFile = $filesystem->read('composer.json');
        } catch (FileNotFoundException $e) {
            return $this->failed('File `composer.json` not found in the repository');
        }

        $composer = @json_decode($composerFile, true);
        if (empty($composer)) {
            return $this->failed('File `composer.json` is not a valid JSON file');
        }

        if (!isset($composer['require']) || !isset($composer['require']['symfony/flex'])) {
            return $this->failed('`symfony/flex` is not a dependency of your project');
        }

        if (!$filesystem->has('.env.dist')) {
            return $this->failed('File `.env.dist` do not exists in your repository');
        }

        $dockerFileLines = [
            'FROM quay.io/continuouspipe/symfony-flex:latest',
        ];

        foreach ($context['env'] as $key => $value) {
            $dockerFileLines[] = 'ARG '.$key;
        }

        $dockerFileLines[] = 'COPY . /app/';
        $dockerFileLines[] = 'WORKDIR /app';
        $dockerFileLines[] = 'RUN container build';

        return [
            GeneratedFile::generated('Dockerfile', implode("\n", $dockerFileLines)),
        ];
    }

    private function failed(string $message)
    {
        return [
            GeneratedFile::failed('Dockerfile', $message),
        ];
    }
}
