<?php

namespace ContinuousPipe\Flex\Bridge\Symfony\Command;

use ContinuousPipe\Flex\ConfigurationGeneration\GenerationException;
use ContinuousPipe\Flex\ConfigurationGeneration\Sequentially\SequentiallyGenerateFiles;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\Context\WithSymfonyContext;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\ContinuousPipeGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\DockerComposeGenerator;
use ContinuousPipe\Flex\ConfigurationGeneration\Symfony\DockerGenerator;
use ContinuousPipe\Flex\Variables\PlainDefinitionGenerator;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName('cp:generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generators = [
            new DockerGenerator(),
            new DockerComposeGenerator(),
        ];

        $context = [];
        if ($this->askIf($input, $output, 'Do you want to generate the ContinuousPipe configuration? ', true)) {
            $generators[] = new ContinuousPipeGenerator(new PlainDefinitionGenerator());

            $questionHelper = new QuestionHelper();
            $context['image_name'] = $questionHelper->ask($input, $output, new Question('What is the Docker image name you want to build? ', 'docker.io/your-username/image-name'));
            $context['endpoint_host_suffix'] = $questionHelper->ask($input, $output, new Question('Give a unique identifier to your project: ', dirname(getcwd())));
            $context['cluster'] = $questionHelper->ask($input, $output, new Question('Which of you cluster do you want to deploy to? ', 'flex'));
            $context['variables'] = [];
        }

        $fileSystem = new Filesystem(new Local(getcwd()));
        $generator = new WithSymfonyContext(
            new SequentiallyGenerateFiles($generators)
        );

        try {
            $generatedConfiguration = $generator->generate($fileSystem, $context);
        } catch (GenerationException $error) {
            $output->writeln([
                '',
                '<error>'.$error->getMessage().'</error>',
                '',
            ]);

            return 1;
        }

        $wroteFiles = [];
        foreach ($generatedConfiguration->getGeneratedFiles() as $generatedFile) {
            if ($generatedFile->hasFailed()) {
                $output->writeln([
                    '',
                    '<error>Generation of file '.$generatedFile->getPath().' has failed: '.$generatedFile->getFailureReason().'</error>',
                    '',
                ]);

                break;
            }

            if ($fileSystem->has($generatedFile->getPath())) {
                if (!$this->askIf($input, $output, sprintf('Do you want to overwrite your "%s" file? ', $generatedFile->getPath()))) {
                    continue;
                }
            }

            $fileSystem->write($generatedFile->getPath(), $generatedFile->getContents());
            $wroteFiles[] = $generatedFile->getPath();
        }

        $output->writeln([
            sprintf('<info>Successfully wrote the following files: %s', implode(', ', $wroteFiles)),
        ]);

        return 0;
    }

    private function askIf(InputInterface $input, OutputInterface $output, string $question, bool $default = true) : bool
    {
        $response = (new QuestionHelper())->ask($input, $output, new Question($question, $default ? 'yes' : 'no'));

        return in_array($response, ['yes', 'y']);
    }
}
