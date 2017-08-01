<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use ContinuousPipe\Flex\ConfigurationGeneration\ConfigurationGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\GeneratedConfiguration;
use ContinuousPipe\Flex\ConfigurationGeneration\Sequentially\SequentiallyGenerateFiles;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\Context\WithSymfonyContext;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\ContinuousPipeGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\DockerComposeGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\DockerGenerator;
use ContinuousPipe\Flex\FileSystem\OverwrittenFileSystem;
use ContinuousPipe\Flex\Variables\PlainDefinitionGenerator;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @var ConfigurationGenerator
     */
    private $configurationGenerator;

    /**
     * @var FilesystemInterface|null
     */
    private $fileSystem;

    /**
     * @var GeneratedConfiguration|null
     */
    private $generatedConfiguration;

    public function __construct()
    {
        $this->configurationGenerator = new WithSymfonyContext(
            new SequentiallyGenerateFiles([
                new DockerGenerator(),
                new DockerComposeGenerator(),
                new ContinuousPipeGenerator(new PlainDefinitionGenerator()),
            ])
        );
    }

    /**
     * @Given the filesystem looks like the :fixtureFolder fixtures folder
     */
    public function theFilesystemLooksLikeTheFixturesFolder($folder)
    {
        $this->fileSystem = new Filesystem(new Local(
            __DIR__.'/../fixtures/'.$folder
        ));
    }

    /**
     * @Given the :fileName file in the filesystem contains:
     */
    public function theFileInTheFilesystemContains($fileName, PyStringNode $string)
    {
        if (!$this->fileSystem instanceof OverwrittenFileSystem) {
            $this->fileSystem = new OverwrittenFileSystem($this->fileSystem);
        }

        $this->fileSystem->setOverwrittenFile($fileName, $string->getRaw());
    }

    /**
     * @When I generate the configuration files
     */
    public function iGenerateTheConfigurationFiles()
    {
        $this->generatedConfiguration = $this->configurationGenerator->generate(
            $this->fileSystem,
            [
                'endpoint_host_suffix' => 'my-app',
                'image_name' => 'docker.io/my/app',
                'cluster' => 'my-kubernetes-cluster',
            ]
        );
    }

    /**
     * @Then the generated :fileName file should look like:
     */
    public function theGeneratedFileShouldLookLike($fileName, PyStringNode $string)
    {
        $contents = $this->getGeneratedContents($fileName);

        if ($string->getRaw() != $contents) {
            throw new RuntimeException('Found the following content instead: '.$contents);
        }
    }

    /**
     * @Then the generated :fileName file should contain at least the following YAML:
     */
    public function theGeneratedFileShouldContainAtLeastTheFollowingYaml($fileName, PyStringNode $string)
    {
        $contents = Yaml::parse($this->getGeneratedContents($fileName));
        $expectedConfiguration = Yaml::parse($string->getRaw());
        $intersection = array_intersect_recursive($expectedConfiguration, $contents);

        if ($intersection != $expectedConfiguration) {
            print_r($intersection);

            throw new \RuntimeException(sprintf(
                'Expected to have at least this configuration but found: %s',
                PHP_EOL . Yaml::dump($contents)
            ));
        }
    }

    private function getGeneratedContents(string $fileName) : string
    {
        foreach ($this->generatedConfiguration->getGeneratedFiles() as $generatedFile) {
            if ($generatedFile->getPath() == $fileName) {
                if ($generatedFile->hasFailed()) {
                    throw new \RuntimeException('Generation failed: '.$generatedFile->getFailureReason());
                }
                
                return $generatedFile->getContents();
            }
        }

        throw new \RuntimeException('File is not found');
    }
}

function array_intersect_recursive($array1, $array2)
{
    foreach($array1 as $key => $value)
    {
        if (!isset($array2[$key]))
        {
            unset($array1[$key]);
        }
        else
        {
            if (is_array($array1[$key]))
            {
                $array1[$key] = array_intersect_recursive($array1[$key], $array2[$key]);
            }
            elseif ($array2[$key] !== $value)
            {
                unset($array1[$key]);
            }
        }
    }
    return $array1;
}
