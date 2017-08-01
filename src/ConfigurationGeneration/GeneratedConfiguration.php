<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration;

final class GeneratedConfiguration
{
    /**
     * @var GeneratedFile[]
     */
    private $generatedFiles;

    /**
     * @param GeneratedFile[] $generatedFiles
     */
    public function __construct(array $generatedFiles)
    {
        $this->generatedFiles = $generatedFiles;
    }

    /**
     * @return GeneratedFile[]
     */
    public function getGeneratedFiles(): array
    {
        return $this->generatedFiles;
    }
}
