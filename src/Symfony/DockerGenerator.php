<?php

namespace ContinuousPipe\Flex\Symfony;

use ContinuousPipe\Flex\ConfigurationFileGenerator;
use League\Flysystem\FilesystemInterface;

final class DockerGenerator implements ConfigurationFileGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $filesystem, array $context)
    {
        $dockerFileLines = [
            'FROM quay.io/continuouspipe/symfony-flex:latest',
        ];

        foreach ($context['env'] as $key => $value) {
            $dockerFileLines[] = 'ARG '.$key;
        }

        $dockerFileLines[] = 'COPY . /app/';
        $dockerFileLines[] = 'WORKDIR /app';
        $dockerFileLines[] = 'RUN container build';

        return implode("\n", $dockerFileLines);
    }
}
