<?php

namespace ContinuousPipe\Flex\Symfony;

use ContinuousPipe\Flex\ConfigurationFileGenerator;
use ContinuousPipe\Flex\FlexException;
use ContinuousPipe\Flex\Variables\VariableDefinitionGenerator;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Generate the ContinuousPipe configuration.
 *
 * This generator relies on the following context variables:
 * - `image_name` (the Docker image name)
 * - `endpoint_host_suffix` (the host endpoint to be used as ingress)
 * - `cluster` (the cluster name)
 * - `variables` (default variable values)
 *
 */
class ContinuousPipeGenerator implements ConfigurationFileGenerator
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
    public function generate(FilesystemInterface $filesystem, array $context)
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
                        'cloud_flare_zone' => [
                            'zone_identifier' => '${CLOUD_FLARE_ZONE}',
                            'proxied' => true,
                            'authentication' => [
                                'email' => '${CLOUD_FLARE_EMAIL}',
                                'api_key' => '${CLOUD_FLARE_API_KEY}',
                            ]
                        ],
                        'ingress' => [
                            'class' => 'nginx',
                            'host_suffix' => $context['endpoint_host_suffix'],
                        ],
                        'ssl_certificates' => [
                            [
                                // Self-sign SSL certificates will be generated automatically by ContinuousPipe
                                'name' => 'automatic',
                                'cert' => 'automatic',
                                'key' => 'automatic',
                            ]
                        ]
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

        // Uses CloudFlare to terminate the SSL connection
        $appDeployServices['app']['endpoints'][0]['cloud_flare_zone']['proxied'] = true;
        $context['env']['WEB_REVERSE_PROXIED'] = true;

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

        $defaults = array_merge([
            'cluster' => $context['cluster'],
        ], isset($context['continuous_pipe_defaults']) ? $context['continuous_pipe_defaults'] : []);

        return Yaml::dump([
            'variables' => $this->generateVariables($context),
            'defaults' => $defaults,
            'tasks' => $tasks,
        ]);
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
