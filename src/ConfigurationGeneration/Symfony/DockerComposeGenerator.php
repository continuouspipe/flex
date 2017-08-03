<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Symfony;

use ContinuousPipe\Flex\ConfigurationGeneration\FileGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedFile;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Yaml\Yaml;

final class DockerComposeGenerator implements FileGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $filesystem, array $context) : array
    {
        $dockerComposeServices = [];
        if (isset($context['env']['DATABASE_URL'])) {
            // Opinionated choice, using postgres. Could/should be using MySQL or anything else
            // guessed instead.
            $context['env']['DATABASE_URL'] = 'postgres://app:app@database/app';

            $dockerComposeServices['database'] = [
                'image' => 'postgres',
                'environment' => [
                    'POSTGRES_PASSWORD=app',
                    'POSTGRES_USER=app',
                    'POSTGRES_DB=app',
                ],
                'expose' => [
                    5432,
                ]
            ];
        }

        $dockerComposeServices['app'] = [
            'build' => '.',
            'environment' => $this->generateDockerComposeEnvironmentFromVariables($context['env']),
            'expose' => [
                80,
            ],
        ];

        $dockerComposeFile = [
            'version' => '2',
            'services' => $dockerComposeServices,
        ];


        return [
            GeneratedFile::generated('docker-compose.yml', Yaml::dump($dockerComposeFile, 6)),
        ];
    }

    private function generateDockerComposeEnvironmentFromVariables(array $variables) : array
    {
        $variableDefinitions = [];

        foreach ($variables as $key => $value) {
            $variableDefinitions[] = $key.'='.$value;
        }

        return $variableDefinitions;
    }
}
