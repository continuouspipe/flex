<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration\Symfony;

use ContinuousPipe\Flex\ConfigurationFileGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\FileGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedFile;
use ContinuousPipe\Flex\FlexException;
use ContinuousPipe\Flex\Variables\VariableDefinitionGenerator;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Generate the ContinuousPipe configuration.
 *
 * This generator relies on the following context variables:
 * - `image_name` (the Docker image name)
 * - `variables` (default variable values)
 *
 */
class ContinuousPipeGenerator implements FileGenerator
{
    /**
     * @var VariableDefinitionGenerator
     */
    private $variableDefinitionGenerator;

    /**
     * @param VariableDefinitionGenerator $variableDefinitionGenerator
     */
    public function __construct(VariableDefinitionGenerator $variableDefinitionGenerator)
    {
        $this->variableDefinitionGenerator = $variableDefinitionGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(FilesystemInterface $filesystem, array $context) : array
    {
        // Adding (for now) all the environment variables as build arguments as well.
        // This could/should be prevented in the future.
        $buildVariables = [];
        foreach ($context['env'] as $variableName => $value) {
            $buildVariables[] = [
                'name' => $variableName,
                'value' => '${'.$variableName.'?:'.$value.'}',
            ];
        }

        $appDeployServices = [
            'app' => [
                'endpoints' => [
                    [
                        'name' => 'app',
                    ]
                ],
                'deployment_strategy' => [
                    'readiness_probe' => [
                        'type' => 'tcp',
                        'port' => 80,
                    ],
                ],
            ]
        ];

        $tasks = [
            '00_images' => [
                'build' => [
                    'services' => [
                        'app' => [
                            'image' => $context['image_name'],
                            'naming_strategy' => 'sha1',
                            'environment' => $buildVariables
                        ],
                    ],
                ]
            ],
            '10_app_deployment' => [
                'deploy' => [
                    'services' => $appDeployServices
                ]
            ]
        ];

        if (isset($context['env']['DATABASE_URL'])) {
            $tasks['05_database_deployment'] = [
                'deploy' => [
                    'services' => [
                        'database' => [
                            'deployment_strategy' => [
                                'readiness_probe' => [
                                    'type' => 'tcp',
                                    'port' => 5432,
                                ],
                            ],
                        ]
                    ]
                ]
            ];
        }

        // Sort tasks by name
        ksort($tasks);

        $defaults = array_merge([], isset($context['cluster']) ? [
            'cluster' => $context['cluster'],
        ] : [], isset($context['continuous_pipe_defaults']) ? $context['continuous_pipe_defaults'] : []);

        return [
            GeneratedFile::generated('continuous-pipe.yml', Yaml::dump([
                'variables' => $this->generateVariables($context),
                'defaults' => $defaults,
                'tasks' => $tasks,
            ], 6)),
        ];
    }

    private function generateVariables($context)
    {
        if (!isset($context['variables'])) {
            return [];
        }

        $variableDefinitions = [];
        foreach ($context['variables'] as $name => $value) {
            $variableDefinitions[] = $this->variableDefinitionGenerator->generateDefinition($name, $value);
        }

        return $variableDefinitions;
    }
}
