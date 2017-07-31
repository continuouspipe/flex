<?php

namespace ContinuousPipe\Flex\Bridge\Symfony\Command;

use ContinuousPipe\Flex\FlexException;
use ContinuousPipe\Flex\GenerateFilesAsPerGeneratorMapping;
use ContinuousPipe\Flex\Symfony\ContinuousPipeGenerator;
use ContinuousPipe\Flex\Symfony\DockerComposeGenerator;
use ContinuousPipe\Flex\Symfony\DockerGenerator;
use ContinuousPipe\Flex\Symfony\GenerateFilesWithSymfonyContext;
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
        $generatorMapping = [
            'Dockerfile' => new DockerGenerator(),
            'docker-compose.yml' => new DockerComposeGenerator(),
        ];

        $context = [];
        if ($this->askIf($input, $output, 'Do you want to generate the ContinuousPipe configuration? ', true)) {
            $generatorMapping['continuous-pipe.yml'] = new ContinuousPipeGenerator(new PlainDefinitionGenerator());

            $questionHelper = new QuestionHelper();
            $context['image_name'] = $questionHelper->ask($input, $output, new Question('What is the Docker image name you want to build? ', 'docker.io/your-username/image-name'));
            $context['endpoint_host_suffix'] = $questionHelper->ask($input, $output, new Question('Give a unique identifier to your project: ', dirname(getcwd())));
            $context['cluster'] = $questionHelper->ask($input, $output, new Question('Which of you cluster do you want to deploy to? ', 'flex'));
            $context['variables'] = [];
        }

        $fileSystem = new Filesystem(new Local(getcwd()));
        $generator = new GenerateFilesWithSymfonyContext(
            new GenerateFilesAsPerGeneratorMapping($generatorMapping)
        );

        if (null !== ($error = $generator->checkAvailability($fileSystem, $context))) {
            $output->writeln([
                '',
                '<error>'.$error->getMessage().'</error>',
                '',
            ]);

            return 1;
        }

        try {
            $files = $generator->generate($fileSystem, $context);
        } catch (FlexException $error) {
            $output->writeln([
                '',
                '<error>'.$error->getMessage().'</error>',
                '',
            ]);

            return 1;
        }

        $wroteFiles = [];
        foreach ($files as $filePath => $contents) {
            if ($fileSystem->has($filePath)) {
                if (!$this->askIf($input, $output, sprintf('Do you want to overwrite your "%s" file? ', $filePath))) {
                    continue;
                }
            }

            $fileSystem->write($filePath, $contents);
            $wroteFiles[] = $filePath;
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
