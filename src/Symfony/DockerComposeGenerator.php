<?php

namespace ContinuousPipe\Flex\Symfony;

use ContinuousPipe\Flex\ConfigurationFileGenerator;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Yaml\Yaml;

final class DockerComposeGenerator implements ConfigurationFileGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $filesystem, array $context)
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


        return Yaml::dump($dockerComposeFile);
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
